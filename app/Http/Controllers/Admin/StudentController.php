<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\Student;
use App\Exports\StudentsExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\StudentsImportTemplateExport;
use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\StudentAcademicYear;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentController extends Controller
{
    /**
     * Menampilkan daftar siswa.
     */
    /**
     * Menampilkan daftar siswa.
     */
    public function index(Request $request): View
    {
        /*
    |--------------------------------------------------------------------------
    | Organisasi yang menjadi kewenangan user
    |--------------------------------------------------------------------------
    */

        $organizationIds =
            Student::organizationIdsForUser();


        /*
    |--------------------------------------------------------------------------
    | Query siswa
    |--------------------------------------------------------------------------
    */

        $query = Student::query()
            ->with([
                'organization',

                'studentAcademicYears' => function ($query) {

                    $query
                        ->with([
                            'academicYear',
                            'schoolClass',
                            'organization',
                        ])
                        ->where(
                            'status',
                            'active'
                        )
                        ->orderByDesc('id');
                },
            ])
            ->forCurrentUser();


        /*
    |--------------------------------------------------------------------------
    | Filter pencarian
    |--------------------------------------------------------------------------
    */

        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(
                function ($q) use ($search) {

                    $q->where(
                        'nis',
                        'like',
                        "%{$search}%"
                    )
                        ->orWhere(
                            'name',
                            'like',
                            "%{$search}%"
                        );
                }
            );
        }


        /*
    |--------------------------------------------------------------------------
    | Filter organisasi
    |--------------------------------------------------------------------------
    */

        if ($request->filled('organization_id')) {

            $organizationId =
                (int) $request->organization_id;

            /*
        | Hanya gunakan organisasi yang memang
        | menjadi kewenangan user.
        */

            if (
                $organizationIds->contains(
                    $organizationId
                )
            ) {

                $query->whereHas(
                    'studentAcademicYears',
                    function ($q) use (
                        $organizationId
                    ) {

                        $q->where(
                            'organization_id',
                            $organizationId
                        )
                            ->where(
                                'status',
                                'active'
                            );
                    }
                );
            }
        }


        /*
    |--------------------------------------------------------------------------
    | Filter tahun akademik
    |--------------------------------------------------------------------------
    */

        if ($request->filled('academic_year_id')) {

            $academicYearId =
                (int) $request->academic_year_id;

            $query->whereHas(
                'studentAcademicYears',
                function ($q) use (
                    $academicYearId
                ) {

                    $q->where(
                        'academic_year_id',
                        $academicYearId
                    )
                        ->where(
                            'status',
                            'active'
                        );
                }
            );
        }


        /*
    |--------------------------------------------------------------------------
    | Filter kelas
    |--------------------------------------------------------------------------
    */

        if ($request->filled('school_class_id')) {

            $schoolClassId =
                (int) $request->school_class_id;

            $query->whereHas(
                'studentAcademicYears',
                function ($q) use (
                    $schoolClassId
                ) {

                    $q->where(
                        'school_class_id',
                        $schoolClassId
                    )
                        ->where(
                            'status',
                            'active'
                        );
                }
            );
        }


        /*
    |--------------------------------------------------------------------------
    | Filter status siswa
    |--------------------------------------------------------------------------
    |
    | Filter status hanya berlaku untuk tabel siswa.
    | Summary kelas tetap menampilkan jumlah lengkap
    | siswa aktif dan nonaktif.
    |
    */

        if ($request->filled('status')) {

            $query->where(
                'is_active',
                $request->status === 'active'
            );
        }


        /*
    |--------------------------------------------------------------------------
    | Ambil data siswa
    |--------------------------------------------------------------------------
    */

        $students = $query
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();


        /*
    |--------------------------------------------------------------------------
    | Summary siswa per kelas
    |--------------------------------------------------------------------------
    |
    | Summary menggunakan SchoolClass sebagai dasar.
    |
    | Filter:
    | - Organisasi
    | - Tahun akademik
    | - Kelas
    |
    | Filter status TIDAK diterapkan di sini.
    |
    */

        $summaryQuery = SchoolClass::query()
            ->whereIn(
                'organization_id',
                $organizationIds
            )
            ->where(
                'is_active',
                true
            )
            ->with([
                'academicYear',
            ]);


        /*
    |--------------------------------------------------------------------------
    | Summary - Filter organisasi
    |--------------------------------------------------------------------------
    */

        if ($request->filled('organization_id')) {

            $organizationId =
                (int) $request->organization_id;

            if (
                $organizationIds->contains(
                    $organizationId
                )
            ) {

                $summaryQuery->where(
                    'organization_id',
                    $organizationId
                );
            }
        }


        /*
    |--------------------------------------------------------------------------
    | Summary - Filter tahun akademik
    |--------------------------------------------------------------------------
    */

        if ($request->filled('academic_year_id')) {

            $summaryQuery->where(
                'academic_year_id',
                (int) $request->academic_year_id
            );
        }


        /*
    |--------------------------------------------------------------------------
    | Summary - Filter kelas
    |--------------------------------------------------------------------------
    */

        if ($request->filled('school_class_id')) {

            $summaryQuery->whereKey(
                (int) $request->school_class_id
            );
        }


        /*
    |--------------------------------------------------------------------------
    | Hitung jumlah siswa per kelas
    |--------------------------------------------------------------------------
    */

        $schoolClassSummary = $summaryQuery
            ->withCount([

                /*
            |--------------------------------------------------------------
            | Total siswa
            |--------------------------------------------------------------
            */

                'studentAcademicYears as total_students' =>
                function ($query) {

                    $query->where(
                        'status',
                        'active'
                    );
                },


                /*
            |--------------------------------------------------------------
            | Siswa aktif
            |--------------------------------------------------------------
            */

                'studentAcademicYears as active_students' =>
                function ($query) {

                    $query
                        ->where(
                            'status',
                            'active'
                        )
                        ->whereHas(
                            'student',
                            function ($q) {

                                $q->where(
                                    'is_active',
                                    true
                                );
                            }
                        );
                },


                /*
            |--------------------------------------------------------------
            | Siswa nonaktif
            |--------------------------------------------------------------
            */

                'studentAcademicYears as inactive_students' =>
                function ($query) {

                    $query
                        ->where(
                            'status',
                            'active'
                        )
                        ->whereHas(
                            'student',
                            function ($q) {

                                $q->where(
                                    'is_active',
                                    false
                                );
                            }
                        );
                },

            ])
            ->orderBy(
                'academic_year_id'
            )
            ->orderBy(
                'level'
            )
            ->orderBy(
                'name'
            )
            ->get();


        /*
    |--------------------------------------------------------------------------
    | Organisasi untuk filter
    |--------------------------------------------------------------------------
    */

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
                "CASE WHEN type = 'PMUB' THEN 0 ELSE 1 END"
            )
            ->orderBy(
                'name'
            )
            ->get();


        /*
    |--------------------------------------------------------------------------
    | Tahun akademik untuk filter
    |--------------------------------------------------------------------------
    */

        $academicYears = AcademicYear::query()
            ->where(
                'is_active',
                true
            )
            ->orderByDesc(
                'start_date'
            )
            ->get();


        /*
    |--------------------------------------------------------------------------
    | Kelas untuk filter
    |--------------------------------------------------------------------------
    |
    | Hanya kelas dari organisasi yang menjadi
    | kewenangan user.
    |
    */

        $schoolClasses = SchoolClass::query()
            ->whereIn(
                'organization_id',
                $organizationIds
            )
            ->where(
                'is_active',
                true
            )
            ->with([
                'academicYear',
                'organization',
            ])
            ->orderBy(
                'academic_year_id'
            )
            ->orderBy(
                'level'
            )
            ->orderBy(
                'name'
            )
            ->get();


        /*
    |--------------------------------------------------------------------------
    | View
    |--------------------------------------------------------------------------
    */

        return view(
            'admin.students.index',
            compact(
                'students',
                'organizations',
                'academicYears',
                'schoolClasses',
                'schoolClassSummary'
            )
        );
    }


    /**
     * Menyimpan siswa baru.
     */
    public function store(Request $request)
    {
        $user = $request->user();

        /*
    |--------------------------------------------------------------------------
    | Organisasi yang menjadi kewenangan user
    |--------------------------------------------------------------------------
    */

        $organizationIds = Student::organizationIdsForUser();


        /*
    |--------------------------------------------------------------------------
    | Validasi
    |--------------------------------------------------------------------------
    */

        $validated = $request->validate([

            /*
        |--------------------------------------------------------------------------
        | Organisasi
        |--------------------------------------------------------------------------
        */

            'organization_id' => [
                'required',
                'integer',
                'exists:organizations,id',

                function ($attribute, $value, $fail) use ($organizationIds) {

                    if (
                        ! $organizationIds->contains(
                            (int) $value
                        )
                    ) {

                        $fail(
                            'Anda tidak memiliki akses ke organisasi tersebut.'
                        );
                    }
                },
            ],


            /*
        |--------------------------------------------------------------------------
        | NIS
        |--------------------------------------------------------------------------
        */

            'nis' => [
                'required',
                'string',
                'max:50',

                function ($attribute, $value, $fail) use ($request) {

                    $exists = Student::query()
                        ->where(
                            'organization_id',
                            $request->organization_id
                        )
                        ->where(
                            'nis',
                            trim($value)
                        )
                        ->exists();

                    if ($exists) {

                        $fail(
                            'NIS tersebut sudah digunakan pada organisasi yang dipilih.'
                        );
                    }
                },
            ],


            /*
        |--------------------------------------------------------------------------
        | Nama
        |--------------------------------------------------------------------------
        */

            'name' => [
                'required',
                'string',
                'max:255',
            ],


            /*
        |--------------------------------------------------------------------------
        | Jenis kelamin
        |--------------------------------------------------------------------------
        */

            'gender' => [
                'required',
                'in:L,P',
            ],


            /*
        |--------------------------------------------------------------------------
        | Tempat lahir
        |--------------------------------------------------------------------------
        */

            'birth_place' => [
                'nullable',
                'string',
                'max:100',
            ],


            /*
        |--------------------------------------------------------------------------
        | Tanggal lahir
        |--------------------------------------------------------------------------
        */

            'birth_date' => [
                'nullable',
                'date',
            ],


            /*
        |--------------------------------------------------------------------------
        | Tahun akademik
        |--------------------------------------------------------------------------
        */

            'academic_year_id' => [
                'required',
                'integer',
                'exists:academic_years,id',

                function ($attribute, $value, $fail) {

                    $exists = AcademicYear::query()
                        ->whereKey($value)
                        ->where(
                            'is_active',
                            true
                        )
                        ->exists();

                    if (! $exists) {

                        $fail(
                            'Tahun akademik yang dipilih tidak aktif atau tidak ditemukan.'
                        );
                    }
                },
            ],


            /*
        |--------------------------------------------------------------------------
        | Kelas
        |--------------------------------------------------------------------------
        */

            'school_class_id' => [
                'required',
                'integer',
                'exists:school_classes,id',

                function ($attribute, $value, $fail) use ($request) {

                    $schoolClass = SchoolClass::query()
                        ->whereKey($value)
                        ->where(
                            'is_active',
                            true
                        )
                        ->first();

                    if (! $schoolClass) {

                        $fail(
                            'Kelas yang dipilih tidak aktif atau tidak ditemukan.'
                        );

                        return;
                    }


                    /*
                | Kelas harus berada pada organisasi
                | yang dipilih.
                */

                    if (
                        (int) $schoolClass->organization_id
                        !==
                        (int) $request->organization_id
                    ) {

                        $fail(
                            'Kelas yang dipilih bukan milik organisasi tersebut.'
                        );

                        return;
                    }


                    /*
                | Kelas harus berada pada
                | tahun akademik yang dipilih.
                */

                    if (
                        (int) $schoolClass->academic_year_id
                        !==
                        (int) $request->academic_year_id
                    ) {

                        $fail(
                            'Kelas yang dipilih bukan bagian dari tahun akademik tersebut.'
                        );
                    }
                },
            ],
        ]);


        /*
    |--------------------------------------------------------------------------
    | Simpan siswa + penempatan akademik
    |--------------------------------------------------------------------------
    */

        DB::transaction(function () use ($validated) {

            /*
        |--------------------------------------------------------------------------
        | Buat siswa
        |--------------------------------------------------------------------------
        */

            $student = Student::create([

                'organization_id' =>
                $validated['organization_id'],

                'nis' =>
                trim($validated['nis']),

                'name' =>
                trim($validated['name']),

                'gender' =>
                $validated['gender'],

                'birth_place' =>
                $validated['birth_place']
                    ?? null,

                'birth_date' =>
                $validated['birth_date']
                    ?? null,

                'is_active' =>
                true,
            ]);


            /*
        |--------------------------------------------------------------------------
        | Buat penempatan akademik
        |--------------------------------------------------------------------------
        */

            StudentAcademicYear::create([

                'student_id' =>
                $student->id,

                'academic_year_id' =>
                $validated['academic_year_id'],

                'organization_id' =>
                $validated['organization_id'],

                'school_class_id' =>
                $validated['school_class_id'],

                'status' =>
                'active',

                'started_at' =>
                now()->toDateString(),

                'ended_at' =>
                null,
            ]);
        });


        /*
    |--------------------------------------------------------------------------
    | Response
    |--------------------------------------------------------------------------
    */

        if ($request->expectsJson()) {

            return response()->json([
                'message' =>
                'Data siswa berhasil ditambahkan.',
            ]);
        }


        return redirect()
            ->route(
                'admin.students.index',
                [
                    'search' =>
                    $request->input('search'),

                    'organization_id' =>
                    $request->input('organization_id_filter'),

                    'academic_year_id' =>
                    $request->input('academic_year_id_filter'),

                    'school_class_id' =>
                    $request->input('school_class_id_filter'),

                    'status' =>
                    $request->input('status_filter'),
                ]
            )
            ->with(
                'success',
                'Data siswa berhasil ditambahkan.'
            );
    }


    /**
     * Memperbarui data siswa.
     */
    public function update(
        Request $request,
        Student $student
    ) {
        /*
    |--------------------------------------------------------------------------
    | Pastikan siswa berada dalam kewenangan user
    |--------------------------------------------------------------------------
    */

        abort_unless(
            Student::forCurrentUser()
                ->whereKey($student->id)
                ->exists(),
            403
        );


        $organizationIds =
            Student::organizationIdsForUser();


        /*
    |--------------------------------------------------------------------------
    | Validasi
    |--------------------------------------------------------------------------
    */

        $validated = $request->validate([

            'organization_id' => [
                'required',
                'integer',
                'exists:organizations,id',

                function ($attribute, $value, $fail) use ($organizationIds) {

                    if (! $organizationIds->contains((int) $value)) {

                        $fail(
                            'Anda tidak memiliki akses ke organisasi tersebut.'
                        );
                    }
                },
            ],

            'nis' => [
                'required',
                'string',
                'max:50',

                'unique:students,nis,'
                    . $student->id
                    . ',id,organization_id,'
                    . $request->organization_id,
            ],

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'birth_place' => [
                'nullable',
                'string',
                'max:100',
            ],

            'gender' => [
                'required',
                'in:L,P',
            ],

            'birth_date' => [
                'nullable',
                'date',
            ],

            'academic_year_id' => [
                'required',
                'integer',
                'exists:academic_years,id',
            ],

            'school_class_id' => [
                'required',
                'integer',
                'exists:school_classes,id',
            ],

            'is_active' => [
                'required',
                'boolean',
            ],
        ]);


        /*
    |--------------------------------------------------------------------------
    | Pastikan tahun akademik aktif
    |--------------------------------------------------------------------------
    */

        $academicYear = AcademicYear::query()
            ->whereKey(
                $validated['academic_year_id']
            )
            ->where(
                'is_active',
                true
            )
            ->first();

        if (! $academicYear) {

            return back()
                ->withErrors([
                    'academic_year_id' =>
                    'Tahun akademik tidak aktif atau tidak tersedia.',
                ])
                ->withInput();
        }


        /*
    |--------------------------------------------------------------------------
    | Pastikan kelas sesuai unit + tahun
    |--------------------------------------------------------------------------
    */

        $schoolClass = SchoolClass::query()
            ->whereKey(
                $validated['school_class_id']
            )
            ->where(
                'organization_id',
                $validated['organization_id']
            )
            ->where(
                'academic_year_id',
                $validated['academic_year_id']
            )
            ->where(
                'is_active',
                true
            )
            ->first();

        if (! $schoolClass) {

            return back()
                ->withErrors([
                    'school_class_id' =>
                    'Kelas tidak sesuai dengan unit dan tahun akademik yang dipilih.',
                ])
                ->withInput();
        }


        /*
    |--------------------------------------------------------------------------
    | Simpan
    |--------------------------------------------------------------------------
    */

        DB::transaction(function () use (
            $student,
            $validated
        ) {

            /*
        | Update identitas siswa
        */

            $student->update([

                'organization_id' =>
                $validated['organization_id'],

                'nis' =>
                $validated['nis'],

                'name' =>
                $validated['name'],

                'birth_place' =>
                $validated['birth_place'] ?? null,

                'gender' =>
                $validated['gender'],

                'birth_date' =>
                $validated['birth_date'] ?? null,

                'is_active' =>
                $validated['is_active'],
            ]);


            /*
        |--------------------------------------------------------------------------
        | Cari record akademik pada tahun yang dipilih
        |--------------------------------------------------------------------------
        */

            $studentAcademicYear =
                StudentAcademicYear::query()
                ->where(
                    'student_id',
                    $student->id
                )
                ->where(
                    'academic_year_id',
                    $validated['academic_year_id']
                )
                ->first();


            /*
        |--------------------------------------------------------------------------
        | Jika belum ada → buat
        |--------------------------------------------------------------------------
        */

            if (! $studentAcademicYear) {

                StudentAcademicYear::create([

                    'student_id' =>
                    $student->id,

                    'academic_year_id' =>
                    $validated['academic_year_id'],

                    'organization_id' =>
                    $validated['organization_id'],

                    'school_class_id' =>
                    $validated['school_class_id'],

                    'status' =>
                    'active',

                    'started_at' =>
                    now()->toDateString(),

                    'ended_at' =>
                    null,
                ]);

                return;
            }


            /*
        |--------------------------------------------------------------------------
        | Jika sudah ada → update penempatannya
        |--------------------------------------------------------------------------
        */

            $studentAcademicYear->update([

                'organization_id' =>
                $validated['organization_id'],

                'school_class_id' =>
                $validated['school_class_id'],

                'status' =>
                'active',

                'ended_at' =>
                null,
            ]);
        });


        if ($request->expectsJson()) {

            return response()->json([
                'message' =>
                'Data siswa berhasil diperbarui.',
            ]);
        }

        return redirect()
            ->route('admin.students.index')
            ->with(
                'success',
                'Data siswa berhasil diperbarui.'
            );
    }

    public function toggleStatus(Student $student)
    {
        // Pastikan siswa berada dalam organisasi
        // yang boleh diakses oleh user saat ini.
        abort_unless(
            Student::forCurrentUser()
                ->whereKey($student->id)
                ->exists(),
            403
        );

        // Ubah status aktif menjadi sebaliknya.
        $student->update([
            'is_active' => ! $student->is_active,
        ]);

        $status = $student->is_active
            ? 'diaktifkan'
            : 'dinonaktifkan';

        return back()->with(
            'success',
            "Peserta {$student->name} berhasil {$status}."
        );
    }

    public function export(Request $request)
    {
        $organizationIds =
            Student::organizationIdsForUser();

        $query = Student::query()
            ->with([
                'organization',
                'studentAcademicYears.academicYear',
                'studentAcademicYears.schoolClass',
            ])
            ->forCurrentUser();


        /*
    |--------------------------------------------------------------------------
    | Pencarian
    |--------------------------------------------------------------------------
    */

        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where(
                    'nis',
                    'like',
                    "%{$search}%"
                )
                    ->orWhere(
                        'name',
                        'like',
                        "%{$search}%"
                    );
            });
        }


        /*
    |--------------------------------------------------------------------------
    | Organisasi
    |--------------------------------------------------------------------------
    */

        if ($request->filled('organization_id')) {

            $organizationId =
                (int) $request->organization_id;

            if (
                $organizationIds->contains(
                    $organizationId
                )
            ) {

                $query->whereHas(
                    'studentAcademicYears',
                    function ($q) use (
                        $organizationId
                    ) {

                        $q->where(
                            'organization_id',
                            $organizationId
                        )
                            ->where(
                                'status',
                                'active'
                            );
                    }
                );
            }
        }


        /*
    |--------------------------------------------------------------------------
    | Tahun akademik
    |--------------------------------------------------------------------------
    */

        if ($request->filled('academic_year_id')) {

            $academicYearId =
                (int) $request->academic_year_id;

            $query->whereHas(
                'studentAcademicYears',
                function ($q) use (
                    $academicYearId
                ) {

                    $q->where(
                        'academic_year_id',
                        $academicYearId
                    )
                        ->where(
                            'status',
                            'active'
                        );
                }
            );
        }


        /*
    |--------------------------------------------------------------------------
    | Kelas
    |--------------------------------------------------------------------------
    */

        if ($request->filled('school_class_id')) {

            $schoolClassId =
                (int) $request->school_class_id;

            $query->whereHas(
                'studentAcademicYears',
                function ($q) use (
                    $schoolClassId
                ) {

                    $q->where(
                        'school_class_id',
                        $schoolClassId
                    )
                        ->where(
                            'status',
                            'active'
                        );
                }
            );
        }


        /*
    |--------------------------------------------------------------------------
    | Status
    |--------------------------------------------------------------------------
    */

        if ($request->filled('status')) {

            $query->where(
                'is_active',
                $request->status === 'active'
            );
        }


        return Excel::download(
            new StudentsExport($query),
            'data-siswa.xlsx'
        );
    }

    public function downloadTemplate()
    {
        return Excel::download(
            new StudentsImportTemplateExport(),
            'template-import-peserta.xlsx'
        );
    }
}
