<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Organization;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class SchoolClassController extends Controller
{
    /**
     * Menampilkan daftar kelas.
     */
    public function index(Request $request): View
    {
        $organizationIds = Organization::accessibleIdsForUser();

        $query = SchoolClass::query()
            ->with([
                'organization',
                'academicYear',
            ])
            ->whereIn(
                'organization_id',
                $organizationIds
            )
            ->where('is_alumni', false);

        /*
        |--------------------------------------------------------------------------
        | Pencarian nama kelas
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(
                'name',
                'like',
                "%{$search}%"
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Filter tahun ajaran
        |--------------------------------------------------------------------------
        */

        if ($request->filled('academic_year_id')) {

            $query->where(
                'academic_year_id',
                $request->academic_year_id
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Filter organisasi
        |--------------------------------------------------------------------------
        */

        if ($request->filled('organization_id')) {

            $organizationId = (int) $request->organization_id;

            if (
                $organizationIds->contains(
                    $organizationId
                )
            ) {
                $query->where(
                    'organization_id',
                    $organizationId
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Filter status
        |--------------------------------------------------------------------------
        */

        if ($request->filled('status')) {

            $query->where(
                'is_active',
                $request->status === 'active'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Organisasi untuk filter
        |--------------------------------------------------------------------------
        */

        $organizations = Organization::whereIn(
            'id',
            $organizationIds
        )
            ->where(
                'is_active',
                true
            )
            ->orderByRaw(
                'parent_id IS NOT NULL'
            )
            ->orderBy('name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Tahun ajaran untuk filter
        |--------------------------------------------------------------------------
        |
        | Tahun ajaran tetap ditampilkan meskipun sudah ditutup,
        | karena histori harus tetap dapat dilihat.
        |
        */

        $academicYears = AcademicYear::query()
            ->orderByDesc('start_date')
            ->get();



        $schoolClasses = $query
            ->withCount('studentAcademicYears')
            ->orderBy('academic_year_id')
            ->orderBy('organization_id')
            ->orderBy('level')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view(
            'admin.school_classes.index',
            compact(
                'schoolClasses',
                'organizations',
                'academicYears'
            )
        );
    }

    /**
     * Form membuat banyak kelas sekaligus.
     */
    public function bulkCreate(): View
    {
        $organizationIds =
            Organization::accessibleIdsForUser();

        $organizations = Organization::query()
            ->whereIn(
                'id',
                $organizationIds
            )
            ->where(
                'is_active',
                true
            )
            ->orderByRaw(
                'parent_id IS NOT NULL'
            )
            ->orderBy('name')
            ->get();

        $academicYears = AcademicYear::query()
            ->where(
                'is_active',
                true
            )
            ->orderByDesc('start_date')
            ->get();

        return view(
            'admin.school_classes.bulk-create',
            compact(
                'organizations',
                'academicYears'
            )
        );
    }

    /**
     * Menyimpan banyak kelas sekaligus.
     */
    public function bulkStore(Request $request)
    {
        $organizationIds =
            Organization::accessibleIdsForUser();

        $validated = $request->validate([
            'organization_id' => [
                'required',
                'integer',
                Rule::in(
                    $organizationIds->all()
                ),
            ],

            'academic_year_id' => [
                'required',
                'integer',
                'exists:academic_years,id',

                function (
                    $attribute,
                    $value,
                    $fail
                ) {
                    $academicYear =
                        AcademicYear::find($value);

                    if (
                        ! $academicYear ||
                        ! $academicYear->is_active
                    ) {
                        $fail(
                            'Tahun ajaran sudah ditutup dan tidak dapat digunakan untuk membuat kelas.'
                        );
                    }
                },
            ],

            'from_level' => [
                'required',
                'integer',
                'min:1',
                'max:12',
            ],

            'to_level' => [
                'required',
                'integer',
                'min:1',
                'max:12',
                'gte:from_level',
            ],

            'class_counts' => [
                'required',
                'array',
            ],

            'class_counts.*' => [
                'required',
                'integer',
                'min:1',
                'max:26',
            ],

            'naming_mode' => [
                'required',
                'string',
                Rule::in([
                    'letter',
                    'number',
                ]),
            ],
        ]);

        /*
    |--------------------------------------------------------------------------
    | Pastikan setiap tingkat memiliki jumlah kelas.
    |--------------------------------------------------------------------------
    */

        for (
            $level = $validated['from_level'];
            $level <= $validated['to_level'];
            $level++
        ) {
            if (
                ! isset(
                    $validated['class_counts'][$level]
                )
            ) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'class_counts' =>
                        "Jumlah kelas untuk tingkat {$level} belum ditentukan.",
                    ]);
            }
        }

        /*
    |--------------------------------------------------------------------------
    | Pastikan semua nama kelas yang akan dibuat belum digunakan.
    |--------------------------------------------------------------------------
    */

        $names = [];

        for (
            $level = $validated['from_level'];
            $level <= $validated['to_level'];
            $level++
        ) {
            $count =
                (int) $validated['class_counts'][$level];

            for (
                $number = 1;
                $number <= $count;
                $number++
            ) {
                if ($validated['naming_mode'] === 'letter') {

                    $suffix = chr(
                        64 + $number
                    );

                    $names[] =
                        $level . $suffix;
                } else {

                    $names[] =
                        $level . '-' . $number;
                }
            }
        }

        $existingNames = SchoolClass::query()
            ->where(
                'organization_id',
                $validated['organization_id']
            )
            ->where(
                'academic_year_id',
                $validated['academic_year_id']
            )
            ->whereIn(
                'name',
                $names
            )
            ->pluck('name');

        if ($existingNames->isNotEmpty()) {
            return back()
                ->withInput()
                ->withErrors([
                    'class_counts' =>
                    'Kelas berikut sudah ada: ' .
                        $existingNames->implode(', ') .
                        '. Silakan sesuaikan jumlah kelas.',
                ]);
        }

        /*
    |--------------------------------------------------------------------------
    | Simpan semua kelas.
    |--------------------------------------------------------------------------
    */

        DB::transaction(function () use (
            $validated
        ) {

            for (
                $level = $validated['from_level'];
                $level <= $validated['to_level'];
                $level++
            ) {
                $count =
                    (int) $validated['class_counts'][$level];

                for (
                    $number = 1;
                    $number <= $count;
                    $number++
                ) {
                    if ($validated['naming_mode'] === 'letter') {

                        $suffix = chr(
                            64 + $number
                        );

                        $className =
                            $level . $suffix;
                    } else {

                        $className =
                            $level . '-' . $number;
                    }

                    SchoolClass::create([
                        'organization_id' =>
                        $validated['organization_id'],

                        'academic_year_id' =>
                        $validated['academic_year_id'],

                        'level' =>
                        $level,

                        'name' =>
                        $className,

                        'is_alumni' =>
                        false,

                        'is_active' =>
                        true,
                    ]);
                }
            }

            /*
    |--------------------------------------------------------------------------
    | Buat kelas Alumni
    |--------------------------------------------------------------------------
    */

            $this->ensureAlumniClass(
                $validated['organization_id'],
                $validated['academic_year_id']
            );
        });

        return redirect()
            ->route(
                'admin.school-classes.index'
            )
            ->with(
                'success',
                'Kelas berhasil dibuat secara massal.'
            );
    }

    /**
     * Form tambah kelas.
     */

    /**
     * Menyimpan kelas baru.
     */
    public function store(Request $request)
    {
        $organizationIds = Organization::accessibleIdsForUser();

        $validated = $request->validate([
            'organization_id' => [
                'required',
                'integer',
                Rule::in(
                    $organizationIds->all()
                ),
            ],

            'academic_year_id' => [
                'required',
                'integer',
                'exists:academic_years,id',

                /*
        |--------------------------------------------------------------------------
        | Tahun ajaran harus terbuka.
        |--------------------------------------------------------------------------
        */

                function (
                    $attribute,
                    $value,
                    $fail
                ) {
                    $academicYear = AcademicYear::find(
                        $value
                    );

                    if (
                        ! $academicYear ||
                        ! $academicYear->is_active
                    ) {
                        $fail(
                            'Tahun ajaran sudah ditutup dan tidak dapat digunakan untuk membuat kelas baru.'
                        );
                    }
                },
            ],

            'level' => [
                'nullable',
                'integer',
                'min:1',
                'max:20',
            ],

            'name' => [
                'required',
                'string',
                'max:100',

                Rule::unique(
                    'school_classes',
                    'name'
                )->where(
                    fn($query) => $query
                        ->where(
                            'organization_id',
                            $request->organization_id
                        )
                        ->where(
                            'academic_year_id',
                            $request->academic_year_id
                        )
                ),
            ],

            'is_active' => [
                'required',
                'boolean',
            ],
        ]);

        $schoolClass = SchoolClass::create([
            'organization_id' => $validated['organization_id'],
            'academic_year_id' => $validated['academic_year_id'],
            'level' => $validated['level'],
            'name' => $validated['name'],
            'is_active' => $validated['is_active'],
            'is_alumni' => false,
        ]);

        $this->ensureAlumniClass(
            $validated['organization_id'],
            $validated['academic_year_id']
        );

        return redirect()
            ->route('admin.school-classes.index')
            ->with(
                'success',
                "Kelas {$schoolClass->name} berhasil ditambahkan."
            );
    }

    /**
     * Form edit kelas.
     */

    /**
     * Memperbarui kelas.
     */
    public function update(
        Request $request,
        SchoolClass $schoolClass
    ) {

        $organizationIds =
            Organization::accessibleIdsForUser();

        /*
        |--------------------------------------------------------------------------
        | Pastikan kelas dapat diakses user.
        |--------------------------------------------------------------------------
        */

        abort_unless(
            $organizationIds->contains(
                $schoolClass->organization_id
            ),
            403
        );

        abort_if(
            $schoolClass->is_alumni,
            403,
            'Kelas Alumni tidak dapat diedit.'
        );

        /*
        |--------------------------------------------------------------------------
        | Tahun ajaran harus masih terbuka.
        |--------------------------------------------------------------------------
        */

        abort_unless(
            $schoolClass->academicYear->is_active,
            403,
            'Kelas berada pada tahun ajaran yang sudah ditutup dan hanya dapat dilihat.'
        );

        $validated = $request->validate([
            'organization_id' => [
                'required',
                'integer',
                Rule::in(
                    $organizationIds->all()
                ),
            ],

            'academic_year_id' => [
                'required',
                'integer',
                'exists:academic_years,id',

                function (
                    $attribute,
                    $value,
                    $fail
                ) {
                    $academicYear =
                        AcademicYear::find($value);

                    if (
                        ! $academicYear ||
                        ! $academicYear->is_active
                    ) {
                        $fail(
                            'Tahun ajaran sudah ditutup dan tidak dapat digunakan.'
                        );
                    }
                },
            ],

            'level' => [
                'nullable',
                'integer',
                'min:1',
                'max:20',
            ],

            'name' => [
                'required',
                'string',
                'max:100',

                Rule::unique(
                    'school_classes',
                    'name'
                )
                    ->where(
                        fn($query) => $query
                            ->where(
                                'organization_id',
                                $request->organization_id
                            )
                            ->where(
                                'academic_year_id',
                                $request->academic_year_id
                            )
                    )
                    ->ignore(
                        $schoolClass->id
                    ),
            ],
        ]);

        $schoolClass->update(
            $validated
        );

        return redirect()
            ->route(
                'admin.school-classes.index'
            )
            ->with(
                'success',
                "Kelas {$schoolClass->name} berhasil diperbarui."
            );
    }

    /**
     * Mengaktifkan atau menonaktifkan kelas.
     */
    public function toggleStatus(
        SchoolClass $schoolClass
    ) {

        $organizationIds =
            Organization::accessibleIdsForUser();

        /*
        |--------------------------------------------------------------------------
        | Pastikan kelas berada dalam kewenangan user.
        |--------------------------------------------------------------------------
        */

        abort_unless(
            $organizationIds->contains(
                $schoolClass->organization_id
            ),
            403
        );

        /*
        |--------------------------------------------------------------------------
        | Tahun ajaran harus terbuka untuk mengubah status kelas.
        |--------------------------------------------------------------------------
        */

        abort_unless(
            $schoolClass->academicYear->is_active,
            403,
            'Kelas berada pada tahun ajaran yang sudah ditutup.'
        );

        $schoolClass->update([
            'is_active' =>
            ! $schoolClass->is_active,
        ]);

        $status = $schoolClass->is_active
            ? 'diaktifkan'
            : 'dinonaktifkan';

        return redirect()
            ->route(
                'admin.school-classes.index'
            )
            ->with(
                'success',
                "Kelas {$schoolClass->name} berhasil {$status}."
            );
    }

    private function ensureAlumniClass(
        int $organizationId,
        int $academicYearId
    ): SchoolClass {
        $organization = Organization::findOrFail($organizationId);
        $academicYear = AcademicYear::findOrFail($academicYearId);

        $alumniName = 'Alumni '
            . $organization->name
            . ' - '
            . $academicYear->name;

        return SchoolClass::firstOrCreate(
            [
                'organization_id' => $organizationId,
                'academic_year_id' => $academicYearId,
                'is_alumni' => true,
            ],
            [
                'level' => null,
                'name' => $alumniName,
                'is_active' => true,
            ]
        );
    }

    /**
     * Menghapus kelas.
     */
    public function destroy(SchoolClass $schoolClass)
    {
        $organizationIds =
            Organization::accessibleIdsForUser();

        /*
    |--------------------------------------------------------------------------
    | Pastikan kelas berada dalam kewenangan user.
    |--------------------------------------------------------------------------
    */

        abort_unless(
            $organizationIds->contains(
                $schoolClass->organization_id
            ),
            403
        );

        abort_if(
            $schoolClass->is_alumni,
            403,
            'Kelas Alumni tidak dapat dihapus.'
        );

        /*
    |--------------------------------------------------------------------------
    | Kelas yang sudah pernah memiliki siswa
    | tidak boleh dihapus.
    |--------------------------------------------------------------------------
    */

        if (
            $schoolClass->studentAcademicYears()->exists()
        ) {
            return back()->with(
                'error',
                "Kelas {$schoolClass->name} tidak dapat dihapus karena sudah memiliki atau pernah memiliki siswa."
            );
        }

        /*
    |--------------------------------------------------------------------------
    | Hapus kelas.
    |--------------------------------------------------------------------------
    */

        $className = $schoolClass->name;

        $schoolClass->delete();

        return back()->with(
            'success',
            "Kelas {$className} berhasil dihapus."
        );
    }

    /**
     * Menghapus kelas secara massal.
     *
     * Kelas yang memiliki atau pernah memiliki siswa
     * akan dilewati dan tidak dihapus.
     */
/**
 * Menghapus kelas secara massal.
 *
 * Kelas yang memiliki atau pernah memiliki siswa
 * akan dilewati.
 */
public function bulkDestroy(Request $request)
{
    $organizationIds =
        Organization::accessibleIdsForUser();

    $validated = $request->validate([
        'class_ids' => [
            'required',
            'array',
            'min:1',
        ],

        'class_ids.*' => [
            'integer',
            'exists:school_classes,id',
        ],
    ], [
        'class_ids.required' =>
            'Silakan pilih minimal satu kelas.',

        'class_ids.min' =>
            'Silakan pilih minimal satu kelas.',

        'class_ids.*.exists' =>
            'Kelas yang dipilih tidak ditemukan.',
    ]);


    $deleted = [];

    $skipped = [];


    /*
    |--------------------------------------------------------------------------
    | Proses setiap kelas
    |--------------------------------------------------------------------------
    */

    DB::transaction(function () use (
        $validated,
        $organizationIds,
        &$deleted,
        &$skipped
    ) {

        foreach ($validated['class_ids'] as $classId) {

            $schoolClass =
                SchoolClass::with('academicYear')
                    ->find($classId);


            /*
            |--------------------------------------------------------------------------
            | Tidak ditemukan
            |--------------------------------------------------------------------------
            */

            if (! $schoolClass) {
                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | Pastikan kelas berada dalam kewenangan user
            |--------------------------------------------------------------------------
            */

            if (
                ! $organizationIds->contains(
                    $schoolClass->organization_id
                )
            ) {
                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | Kelas Alumni
            |--------------------------------------------------------------------------
            */

            if ($schoolClass->is_alumni) {

                $skipped[] =
                    "{$schoolClass->name} — Kelas Alumni.";

                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | Tahun ajaran sudah ditutup
            |--------------------------------------------------------------------------
            */

            if (
                ! $schoolClass->academicYear ||
                ! $schoolClass->academicYear->is_active
            ) {

                $skipped[] =
                    "{$schoolClass->name} — Tahun ajaran sudah ditutup.";

                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | Masih memiliki / pernah memiliki siswa
            |--------------------------------------------------------------------------
            */

            if (
                $schoolClass
                    ->studentAcademicYears()
                    ->exists()
            ) {

                $skipped[] =
                    "{$schoolClass->name} — masih memiliki atau pernah memiliki siswa.";

                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | Hapus
            |--------------------------------------------------------------------------
            */

            $className =
                $schoolClass->name;

            $schoolClass->delete();

            $deleted[] =
                $className;
        }
    });


    /*
    |--------------------------------------------------------------------------
    | Tidak ada yang berhasil dihapus
    |--------------------------------------------------------------------------
    */

    if (
        empty($deleted) &&
        ! empty($skipped)
    ) {

        return back()
            ->with(
                'error',
                'Tidak ada kelas yang dihapus karena seluruh kelas yang dipilih tidak memenuhi syarat penghapusan.'
            )
            ->with(
                'bulk_delete_skipped',
                $skipped
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Berhasil
    |--------------------------------------------------------------------------
    */

    $message =
        count($deleted) .
        ' kelas berhasil dihapus.';


    if (! empty($skipped)) {

        $message .= ' ' .
            count($skipped) .
            ' kelas dilewati.';
    }


    return back()
        ->with(
            'success',
            $message
        )
        ->with(
            'bulk_delete_skipped',
            $skipped
        );
}
}
