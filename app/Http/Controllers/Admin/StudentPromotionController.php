<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Organization;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\StudentAcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class StudentPromotionController extends Controller
{
    /**
     * Menampilkan halaman naik kelas.
     */
    public function index(Request $request): View
    {
        $organizationIds =
            Student::organizationIdsForUser();

        /*
    |--------------------------------------------------------------------------
    | Tahun ajaran
    |--------------------------------------------------------------------------
    */

        $academicYears = AcademicYear::query()
            ->orderByDesc('start_date')
            ->get();

        /*
    |--------------------------------------------------------------------------
    | Tahun ajaran asal
    |--------------------------------------------------------------------------
    */

        $sourceAcademicYearId =
            $request->integer(
                'source_academic_year_id'
            );

        /*
    |--------------------------------------------------------------------------
    | Organisasi
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
            ->orderBy('name')
            ->get();

        $organizationId =
            $request->integer(
                'organization_id'
            );

        /*
    |--------------------------------------------------------------------------
    | Jika user hanya memiliki satu unit,
    | pilih otomatis.
    |--------------------------------------------------------------------------
    */

        if (
            ! $organizationId &&
            $organizationIds->count() === 1
        ) {
            $organizationId =
                $organizationIds->first();
        }

        /*
    |--------------------------------------------------------------------------
    | Jika tahun asal belum dipilih,
    | gunakan tahun ajaran aktif terbaru.
    |--------------------------------------------------------------------------
    */

        if (! $sourceAcademicYearId) {

            $sourceAcademicYearId =
                AcademicYear::query()
                ->where(
                    'is_active',
                    true
                )
                ->orderByDesc(
                    'start_date'
                )
                ->value('id');
        }

        /*
    |--------------------------------------------------------------------------
    | Tahun ajaran asal
    |--------------------------------------------------------------------------
    */

        $sourceAcademicYear =
            $sourceAcademicYearId
            ? AcademicYear::find(
                $sourceAcademicYearId
            )
            : null;

        /*
    |--------------------------------------------------------------------------
    | Tahun ajaran tujuan
    |--------------------------------------------------------------------------
    |
    | Tahun tujuan adalah tahun ajaran
    | setelah tahun asal.
    |--------------------------------------------------------------------------
    */

        $targetAcademicYear = null;

        if ($sourceAcademicYear) {

            $targetAcademicYear =
                AcademicYear::query()
                ->where(
                    'start_date',
                    '>',
                    $sourceAcademicYear->start_date
                )
                ->orderBy(
                    'start_date'
                )
                ->first();
        }

        /*
    |--------------------------------------------------------------------------
    | Kelas asal
    |--------------------------------------------------------------------------
    */

        $sourceClasses = collect();

        if (
            $sourceAcademicYearId &&
            $organizationId &&
            $organizationIds->contains(
                $organizationId
            )
        ) {

            $sourceClasses = SchoolClass::query()
                ->where(
                    'academic_year_id',
                    $sourceAcademicYearId
                )
                ->where(
                    'organization_id',
                    $organizationId
                )
                ->where(
                    'is_active',
                    true
                )
                ->where(
                    'is_alumni',
                    false
                )
                ->orderBy('level')
                ->orderBy('name')
                ->get();
        }

        /*
|--------------------------------------------------------------------------
| Kelas asal yang dipilih
|--------------------------------------------------------------------------
*/

        $sourceClassId = $request->integer(
            'source_class_id'
        );

        $sourceClass = null;

        if (
            $sourceClassId &&
            $sourceClasses->contains(
                'id',
                $sourceClassId
            )
        ) {
            $sourceClass =
                $sourceClasses->firstWhere(
                    'id',
                    $sourceClassId
                );
        }

        /*
|--------------------------------------------------------------------------
| Tingkat terakhir pada unit + tahun ajaran
|--------------------------------------------------------------------------
*/

        $finalLevel = null;

        $isFinalLevel = false;

        if (
            $sourceAcademicYearId &&
            $organizationId
        ) {
            $finalLevel = SchoolClass::query()
                ->where(
                    'organization_id',
                    $organizationId
                )
                ->where(
                    'academic_year_id',
                    $sourceAcademicYearId
                )
                ->where(
                    'is_active',
                    true
                )
                ->where(
                    'is_alumni',
                    false
                )
                ->max('level');

            $isFinalLevel =
                $sourceClass &&
                (int) $sourceClass->level ===
                (int) $finalLevel;
        }

        /*
|--------------------------------------------------------------------------
| Kelas tujuan
|--------------------------------------------------------------------------
|
| Hanya tersedia jika siswa belum berada
| pada tingkat terakhir.
|
| Kelas tujuan berasal dari tahun ajaran tujuan.
|--------------------------------------------------------------------------
*/

        $targetClasses = collect();

        if (
            $sourceClass &&
            $targetAcademicYear &&
            ! $isFinalLevel
        ) {
            $targetClasses = SchoolClass::query()
                ->where(
                    'academic_year_id',
                    $targetAcademicYear->id
                )
                ->where(
                    'organization_id',
                    $organizationId
                )
                ->where(
                    'is_active',
                    true
                )
                ->where(
                    'level',
                    (int) $sourceClass->level + 1
                )
                ->orderBy('name')
                ->get();
        }

        /*
    |--------------------------------------------------------------------------
    | Kelas tujuan yang dipilih
    |--------------------------------------------------------------------------
    */

        $targetClassId =
            $request->integer(
                'target_class_id'
            );

        /*
    |--------------------------------------------------------------------------
    | Jika hanya ada satu kelas tujuan,
    | pilih otomatis.
    |--------------------------------------------------------------------------
    */

        if (
            ! $targetClassId &&
            $targetClasses->count() === 1
        ) {
            $targetClassId =
                $targetClasses->first()->id;
        }

        /*
    |--------------------------------------------------------------------------
    | Siswa pada kelas asal
    |--------------------------------------------------------------------------
    */

        $sourceStudents = collect();

        if ($sourceClass) {

            $sourceStudents =
                StudentAcademicYear::query()
                ->with('student')
                ->where(
                    'academic_year_id',
                    $sourceAcademicYearId
                )
                ->where(
                    'organization_id',
                    $organizationId
                )
                ->where(
                    'school_class_id',
                    $sourceClass->id
                )
                ->where(
                    'status',
                    'active'
                )
                ->whereHas(
                    'student',
                    function ($query) {
                        $query->where(
                            'is_active',
                            true
                        );
                    }
                )
                ->orderBy('id')
                ->get();
        }

        /*
    |--------------------------------------------------------------------------
    | Siswa yang sudah terdaftar
    | pada tahun tujuan
    |--------------------------------------------------------------------------
    */

        $targetStudents = collect();

        if (
            $targetClassId &&
            $targetAcademicYear
        ) {

            $targetStudents =
                StudentAcademicYear::query()
                ->with('student')
                ->where(
                    'academic_year_id',
                    $targetAcademicYear->id
                )
                ->where(
                    'organization_id',
                    $organizationId
                )
                ->where(
                    'school_class_id',
                    $targetClassId
                )
                ->where(
                    'status',
                    'active'
                )
                ->whereHas(
                    'student',
                    function ($query) {
                        $query->where(
                            'is_active',
                            true
                        );
                    }
                )
                ->orderBy('id')
                ->get();
        }

        return view(
            'admin.students.promotion',
            compact(
                'academicYears',
                'organizations',
                'sourceAcademicYear',
                'sourceAcademicYearId',
                'targetAcademicYear',
                'organizationId',
                'sourceClasses',
                'sourceClassId',
                'sourceClass',
                'targetClasses',
                'targetClassId',
                'sourceStudents',
                'targetStudents',
                'finalLevel',
                'isFinalLevel',
                'sourceClass',
            )
        );
    }

    /**
     * Memproses siswa naik ke tahun ajaran berikutnya.
     */
    public function store(Request $request)
    {
        $organizationIds =
            Student::organizationIdsForUser();

        $validated = $request->validate([
            'source_academic_year_id' => [
                'required',
                'integer',
                'exists:academic_years,id',
            ],

            'organization_id' => [
                'required',
                'integer',
                Rule::in(
                    $organizationIds->all()
                ),
            ],

            'source_class_id' => [
                'required',
                'integer',
                'exists:school_classes,id',
            ],

            'target_class_id' => [
                'nullable',
                'integer',

            ],

            'student_ids' => [
                'required',
                'array',
                'min:1',
            ],

            'student_ids.*' => [
                'required',
                'integer',
                'distinct',
                'exists:students,id',
            ],
        ]);

        /*
    |--------------------------------------------------------------------------
    | Tahun ajaran asal
    |--------------------------------------------------------------------------
    */

        $sourceAcademicYear =
            AcademicYear::findOrFail(
                $validated['source_academic_year_id']
            );

        /*
    |--------------------------------------------------------------------------
    | Tahun ajaran tujuan
    |--------------------------------------------------------------------------
    |
    | Untuk siswa yang naik kelas, tahun tujuan adalah
    | tahun ajaran berikutnya.
    |
    */

        $targetAcademicYear =
            AcademicYear::query()
            ->where(
                'start_date',
                '>',
                $sourceAcademicYear->start_date
            )
            ->orderBy(
                'start_date'
            )
            ->first();

        /*
    |--------------------------------------------------------------------------
    | Kelas asal
    |--------------------------------------------------------------------------
    */

        $sourceClass =
            SchoolClass::query()
            ->whereKey(
                $validated['source_class_id']
            )
            ->where(
                'academic_year_id',
                $sourceAcademicYear->id
            )
            ->where(
                'organization_id',
                $validated['organization_id']
            )
            ->where(
                'is_active',
                true
            )
            ->first();

        if (! $sourceClass) {
            return back()
                ->withErrors([
                    'source_class_id' =>
                    'Kelas asal tidak valid.',
                ])
                ->withInput();
        }

        /*
    |--------------------------------------------------------------------------
    | Tentukan tingkat terakhir
    |--------------------------------------------------------------------------
    */

        $finalLevel =
            SchoolClass::query()
            ->where(
                'organization_id',
                $validated['organization_id']
            )
            ->where(
                'academic_year_id',
                $sourceAcademicYear->id
            )
            ->where(
                'is_active',
                true
            )
            ->where(
                'is_alumni',
                false
            )
            ->max('level');

        $isFinalLevel =
            (int) $sourceClass->level ===
            (int) $finalLevel;

        /*
|--------------------------------------------------------------------------
| Kelas Alumni
|--------------------------------------------------------------------------
*/

        $alumniClass = null;

        if ($isFinalLevel) {

            $alumniClass =
                SchoolClass::query()
                ->where(
                    'organization_id',
                    $validated['organization_id']
                )
                ->where(
                    'academic_year_id',
                    $sourceAcademicYear->id
                )
                ->where(
                    'is_alumni',
                    true
                )
                ->where(
                    'is_active',
                    true
                )
                ->first();

            if (! $alumniClass) {
                return back()
                    ->withErrors([
                        'source_class_id' =>
                        'Kelas Alumni untuk unit dan tahun ajaran ini belum tersedia.',
                    ])
                    ->withInput();
            }
        }

        /*
    |--------------------------------------------------------------------------
    | Kelas tujuan
    |--------------------------------------------------------------------------
    |
    | Hanya diperlukan jika siswa BELUM berada pada
    | tingkat terakhir.
    |--------------------------------------------------------------------------
    */

        $targetClass = null;

        if (! $isFinalLevel) {

            /*
        |--------------------------------------------------------------------------
        | Tahun ajaran tujuan harus tersedia
        |--------------------------------------------------------------------------
        */

            if (! $targetAcademicYear) {
                return back()
                    ->withErrors([
                        'target_class_id' =>
                        'Tahun ajaran tujuan belum tersedia.',
                    ])
                    ->withInput();
            }

            /*
        |--------------------------------------------------------------------------
        | Tahun ajaran tujuan harus terbuka
        |--------------------------------------------------------------------------
        */

            if (! $targetAcademicYear->is_active) {
                return back()
                    ->withErrors([
                        'target_class_id' =>
                        'Tahun ajaran tujuan sudah ditutup dan tidak dapat digunakan untuk pengelolaan.',
                    ])
                    ->withInput();
            }

            /*
        |--------------------------------------------------------------------------
        | Kelas tujuan wajib dipilih
        |--------------------------------------------------------------------------
        */

            if (
                ! $request->filled(
                    'target_class_id'
                )
            ) {
                return back()
                    ->withErrors([
                        'target_class_id' =>
                        'Kelas tujuan wajib dipilih untuk siswa yang belum berada di tingkat terakhir.',
                    ])
                    ->withInput();
            }

            /*
        |--------------------------------------------------------------------------
        | Validasi kelas tujuan
        |--------------------------------------------------------------------------
        */

            $targetClass =
                SchoolClass::query()
                ->whereKey(
                    $validated['target_class_id']
                )
                ->where(
                    'academic_year_id',
                    $targetAcademicYear->id
                )
                ->where(
                    'organization_id',
                    $validated['organization_id']
                )
                ->where(
                    'is_active',
                    true
                )
                ->first();

            if (! $targetClass) {
                return back()
                    ->withErrors([
                        'target_class_id' =>
                        'Kelas tujuan tidak valid.',
                    ])
                    ->withInput();
            }

            /*
        |--------------------------------------------------------------------------
        | Kelas tujuan harus tepat satu tingkat di atas
        |--------------------------------------------------------------------------
        */

            if (
                (int) $targetClass->level !==
                ((int) $sourceClass->level + 1)
            ) {
                return back()
                    ->withErrors([
                        'target_class_id' =>
                        'Kelas tujuan harus tepat satu tingkat di atas kelas asal.',
                    ])
                    ->withInput();
            }
        }

        /*
    |--------------------------------------------------------------------------
    | Ambil siswa yang benar-benar berada di kelas asal
    |--------------------------------------------------------------------------
    */

        $studentAcademicYears =
            StudentAcademicYear::query()
            ->where(
                'academic_year_id',
                $sourceAcademicYear->id
            )
            ->where(
                'organization_id',
                $validated['organization_id']
            )
            ->where(
                'school_class_id',
                $sourceClass->id
            )
            ->where(
                'status',
                'active'
            )
            ->whereIn(
                'student_id',
                $validated['student_ids']
            )
            ->whereHas(
                'student',
                function ($query) {
                    $query->where(
                        'is_active',
                        true
                    );
                }
            )
            ->get();

        /*
    |--------------------------------------------------------------------------
    | Semua siswa yang dipilih harus valid
    |--------------------------------------------------------------------------
    */

        if (
            $studentAcademicYears->count() !==
            count($validated['student_ids'])
        ) {
            return back()
                ->withErrors([
                    'student_ids' =>
                    'Sebagian siswa yang dipilih tidak berada pada kelas asal atau tidak aktif.',
                ])
                ->withInput();
        }

        /*
    |--------------------------------------------------------------------------
    | PROSES
    |--------------------------------------------------------------------------
    */

        $createdCount = 0;
        $skippedCount = 0;

        DB::transaction(
            function () use (
                $studentAcademicYears,
                $targetAcademicYear,
                $targetClass,
                $sourceAcademicYear,
                $alumniClass,
                $isFinalLevel,
                &$createdCount,
                &$skippedCount
            ) {

                foreach (
                    $studentAcademicYears
                    as $studentAcademicYear
                ) {

                    /*
                |--------------------------------------------------------------------------
                | JALUR LULUS
                |--------------------------------------------------------------------------
                */

                    if ($isFinalLevel) {

                        $studentAcademicYear->update([
                            'school_class_id' =>
                            $alumniClass->id,

                            'status' =>
                            'graduated',

                            'ended_at' =>
                            $sourceAcademicYear->end_date,
                        ]);

                        $createdCount++;

                        continue;
                    }

                    /*
                |--------------------------------------------------------------------------
                | JALUR NAIK KELAS
                |--------------------------------------------------------------------------
                */

                    $existing =
                        StudentAcademicYear::query()
                        ->where(
                            'student_id',
                            $studentAcademicYear->student_id
                        )
                        ->where(
                            'academic_year_id',
                            $targetAcademicYear->id
                        )
                        ->exists();

                    if ($existing) {

                        $skippedCount++;

                        continue;
                    }

                    /*
                |--------------------------------------------------------------------------
                | Buat record tahun ajaran baru
                |--------------------------------------------------------------------------
                */

                    StudentAcademicYear::create([
                        'student_id' =>
                        $studentAcademicYear->student_id,

                        'academic_year_id' =>
                        $targetAcademicYear->id,

                        'organization_id' =>
                        $targetClass->organization_id,

                        'school_class_id' =>
                        $targetClass->id,

                        'status' =>
                        'active',

                        'started_at' =>
                        $targetAcademicYear->start_date,

                        'ended_at' =>
                        null,
                    ]);

                    $createdCount++;
                }
            }
        );

        /*
    |--------------------------------------------------------------------------
    | Pesan hasil
    |--------------------------------------------------------------------------
    */

        if ($isFinalLevel) {

            $message =
                "{$createdCount} siswa berhasil dinyatakan lulus dari kelas " .
                $sourceClass->name .
                " dan dipindahkan ke kelas Alumni.";
        } else {

            $message =
                "{$createdCount} siswa berhasil dinaikkan ke kelas " .
                $targetClass->name . ".";

            if ($skippedCount > 0) {

                $message .=
                    " {$skippedCount} siswa dilewati karena sudah memiliki data pada tahun ajaran tujuan.";
            }
        }

        /*
    |--------------------------------------------------------------------------
    | Kembali ke halaman
    |--------------------------------------------------------------------------
    */

        $params = [
            'source_academic_year_id' =>
            $sourceAcademicYear->id,

            'organization_id' =>
            $validated['organization_id'],

            'source_class_id' =>
            $sourceClass->id,
        ];

        if (
            ! $isFinalLevel &&
            $targetClass
        ) {
            $params['target_class_id'] =
                $targetClass->id;
        }

        return redirect()
            ->route(
                'admin.students.promotion.index',
                $params
            )
            ->with(
                'success',
                $message
            );
    }
}
