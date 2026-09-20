<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\TeachingAssignment;
use App\Models\Attendance;
use App\Models\AttendanceDetail;
use App\Models\StudentAcademicYear;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    /**
     * Menampilkan penugasan mengajar milik Guru
     * yang dapat digunakan untuk melakukan absensi.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | Cari Guru berdasarkan User
        |--------------------------------------------------------------------------
        */

        $teacher = $user->teacher;

        abort_unless($teacher, 403);


        /*
        |--------------------------------------------------------------------------
        | Filter
        |--------------------------------------------------------------------------
        */

        $academicYearId = $request->input('academic_year_id');
        $schoolClassId = $request->input('school_class_id');
        $subjectId = $request->input('subject_id');


        /*
        |--------------------------------------------------------------------------
        | Tahun Akademik
        |--------------------------------------------------------------------------
        */

        $academicYears = AcademicYear::query()
            ->orderByDesc('start_date')
            ->get();

        /*
        | Tahun akademik terbaru menjadi default.
        */

        if ($academicYearId === null) {
            $academicYearId = $academicYears->first()?->id;
        }


        /*
        |--------------------------------------------------------------------------
        | Teaching Assignment milik Guru
        |--------------------------------------------------------------------------
        */

        $teacherAssignments = TeachingAssignment::query()
            ->where('teacher_id', $teacher->id)
            ->where('is_active', true);


        /*
        |--------------------------------------------------------------------------
        | Filter Tahun Akademik
        |--------------------------------------------------------------------------
        */

        if ($academicYearId !== 'all' && $academicYearId !== null) {

            $teacherAssignments->whereHas(
                'schoolClass',
                function ($query) use ($academicYearId) {
                    $query->where(
                        'academic_year_id',
                        $academicYearId
                    );
                }
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Daftar Kelas untuk Filter
        |--------------------------------------------------------------------------
        |
        | Hanya kelas yang memang diajar oleh Guru.
        |
        */

        $filterClasses = (clone $teacherAssignments)
            ->with('schoolClass')
            ->get()
            ->pluck('schoolClass')
            ->unique('id')
            ->sortBy('name')
            ->values();


        /*
        |--------------------------------------------------------------------------
        | Daftar Mata Pelajaran untuk Filter
        |--------------------------------------------------------------------------
        |
        | Hanya mata pelajaran yang memang diajar oleh Guru.
        |
        */

        $filterSubjects = (clone $teacherAssignments)
            ->with('subject')
            ->get()
            ->pluck('subject')
            ->unique('id')
            ->sortBy('name')
            ->values();


        /*
        |--------------------------------------------------------------------------
        | Filter Kelas
        |--------------------------------------------------------------------------
        */

        if ($schoolClassId !== null && $schoolClassId !== 'all') {

            $teacherAssignments->where(
                'school_class_id',
                $schoolClassId
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Filter Mata Pelajaran
        |--------------------------------------------------------------------------
        */

        if ($subjectId !== null && $subjectId !== 'all') {

            $teacherAssignments->where(
                'subject_id',
                $subjectId
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Teaching Assignment yang Ditampilkan
        |--------------------------------------------------------------------------
        */

        $teachingAssignments = $teacherAssignments
            ->with([
                'schoolClass.organization',
                'schoolClass.academicYear',
                'subject',
            ])
            ->whereHas('schoolClass', function ($query) {

                $query->where('is_active', true)
                    ->where('is_alumni', false);
            })
            ->orderByDesc('id')
            ->get();


        /*
|--------------------------------------------------------------------------
| Riwayat Absensi Siswa
|--------------------------------------------------------------------------
*/

        $attendances = Attendance::query()
            ->with([
                'teachingAssignment.schoolClass.organization',
                'teachingAssignment.schoolClass.academicYear',
                'teachingAssignment.subject',
                'details.studentAcademicYear.student',
            ])
            ->withCount('details')
            ->whereHas('teachingAssignment', function ($query) use ($teacher) {

                $query->where(
                    'teacher_id',
                    $teacher->id
                );
            })
            ->when(
                $academicYearId !== null &&
                    $academicYearId !== 'all',
                function ($query) use ($academicYearId) {

                    $query->whereHas(
                        'teachingAssignment.schoolClass',
                        function ($q) use ($academicYearId) {

                            $q->where(
                                'academic_year_id',
                                $academicYearId
                            );
                        }
                    );
                }
            )
            ->when(
                $schoolClassId !== null &&
                    $schoolClassId !== 'all',
                function ($query) use ($schoolClassId) {

                    $query->whereHas(
                        'teachingAssignment',
                        function ($q) use ($schoolClassId) {

                            $q->where(
                                'school_class_id',
                                $schoolClassId
                            );
                        }
                    );
                }
            )
            ->when(
                $subjectId !== null &&
                    $subjectId !== 'all',
                function ($query) use ($subjectId) {

                    $query->whereHas(
                        'teachingAssignment',
                        function ($q) use ($subjectId) {

                            $q->where(
                                'subject_id',
                                $subjectId
                            );
                        }
                    );
                }
            )
            ->orderByDesc('date')
            ->orderByDesc('created_at')
            ->get();


        return view(
            'guru.attendance.index',
            compact(
                'teachingAssignments',
                'academicYears',
                'academicYearId',
                'filterClasses',
                'filterSubjects',
                'schoolClassId',
                'subjectId',
                'attendances',
            )
        );
    }

    /**
     * Mengambil pilihan kelas dan mata pelajaran
     * berdasarkan tahun akademik yang dipilih.
     */
    public function filterOptions(Request $request)
    {
        $teacher = $request->user()->teacher;

        abort_unless($teacher, 403);

        $academicYearId = $request->input('academic_year_id');

        $query = TeachingAssignment::query()
            ->where('teacher_id', $teacher->id)
            ->where('is_active', true);

        /*
    |--------------------------------------------------------------------------
    | Filter Tahun Akademik
    |--------------------------------------------------------------------------
    */

        if ($academicYearId !== 'all' && $academicYearId !== null) {
            $query->whereHas('schoolClass', function ($query) use ($academicYearId) {
                $query->where('academic_year_id', $academicYearId)
                    ->where('is_active', true)
                    ->where('is_alumni', false);
            });
        } else {
            $query->whereHas('schoolClass', function ($query) {
                $query->where('is_active', true)
                    ->where('is_alumni', false);
            });
        }

        /*
    |--------------------------------------------------------------------------
    | Ambil Kelas dan Mata Pelajaran
    |--------------------------------------------------------------------------
    */

        $assignments = $query
            ->with([
                'schoolClass',
                'subject',
            ])
            ->get();

        $classes = $assignments
            ->pluck('schoolClass')
            ->unique('id')
            ->sortBy('name')
            ->values()
            ->map(function ($class) {
                return [
                    'id' => $class->id,
                    'name' => $class->name,
                ];
            });

        $subjects = $assignments
            ->pluck('subject')
            ->unique('id')
            ->sortBy('name')
            ->values()
            ->map(function ($subject) {
                return [
                    'id' => $subject->id,
                    'name' => $subject->name,
                ];
            });

        return response()->json([
            'classes' => $classes,
            'subjects' => $subjects,
        ]);
    }


    /**
     * Mengambil daftar siswa berdasarkan
     * teaching assignment Guru yang login.
     */
    public function students(
        Request $request,
        TeachingAssignment $teachingAssignment
    ) {
        $teacher = $request->user()->teacher;

        abort_unless($teacher, 403);

        /*
    |--------------------------------------------------------------------------
    | Pastikan Teaching Assignment milik Guru yang login
    |--------------------------------------------------------------------------
    */

        abort_unless(
            $teachingAssignment->teacher_id === $teacher->id,
            403
        );

        abort_unless(
            $teachingAssignment->is_active,
            403
        );


        /*
    |--------------------------------------------------------------------------
    | Pastikan Kelas Aktif dan Bukan Alumni
    |--------------------------------------------------------------------------
    */

        $schoolClass = $teachingAssignment->schoolClass;

        abort_unless($schoolClass, 404);

        abort_unless(
            $schoolClass->is_active && !$schoolClass->is_alumni,
            403
        );


        /*
    |--------------------------------------------------------------------------
    | Ambil Siswa
    |--------------------------------------------------------------------------
    */

        $students = \App\Models\StudentAcademicYear::query()
            ->with('student')
            ->where('school_class_id', $schoolClass->id)
            ->where('academic_year_id', $schoolClass->academic_year_id)
            ->where('status', 'active')
            ->orderBy(
                \App\Models\Student::query()
                    ->select('name')
                    ->whereColumn(
                        'students.id',
                        'student_academic_years.student_id'
                    )
            )
            ->get();


        return response()->json([
            'students' => $students->map(function ($studentAcademicYear) {

                return [
                    'id' => $studentAcademicYear->id,
                    'nis' => $studentAcademicYear->student->nis,
                    'name' => $studentAcademicYear->student->name,
                ];
            })->values(),
        ]);
    }

    /**
     * Menyimpan absensi siswa.
     */
    public function store(Request $request)
    {
        $teacher = $request->user()->teacher;

        abort_unless($teacher, 403);

        /*
    |--------------------------------------------------------------------------
    | Validasi dasar
    |--------------------------------------------------------------------------
    */

        $validated = $request->validate([
            'teaching_assignment_id' => [
                'required',
                'integer',
                'exists:teaching_assignments,id',
            ],

            'date' => [
                'required',
                'date',
            ],

            'meeting_number' => [
                'required',
                'integer',
                'min:1',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'students' => [
                'required',
                'array',
                'min:1',
            ],

            'students.*.student_academic_year_id' => [
                'required',
                'integer',
                'exists:student_academic_years,id',
            ],

            'students.*.status' => [
                'required',
                Rule::in(['present', 'sick', 'permission', 'absent']),
            ],

            'students.*.notes' => [
                'nullable',
                'string',
                'max:500',
            ],
        ]);


        /*
    |--------------------------------------------------------------------------
    | Teaching Assignment
    |--------------------------------------------------------------------------
    */

        $teachingAssignment = TeachingAssignment::query()
            ->with('schoolClass')
            ->where('id', $validated['teaching_assignment_id'])
            ->where('teacher_id', $teacher->id)
            ->where('is_active', true)
            ->firstOrFail();


        /*
    |--------------------------------------------------------------------------
    | Pastikan kelas masih aktif
    |--------------------------------------------------------------------------
    */

        abort_unless(
            $teachingAssignment->schoolClass &&
                $teachingAssignment->schoolClass->is_active &&
                !$teachingAssignment->schoolClass->is_alumni,
            403
        );


        /*
    |--------------------------------------------------------------------------
    | Pastikan siswa memang berasal dari kelas tersebut
    |--------------------------------------------------------------------------
    */

        $studentAcademicYearIds = StudentAcademicYear::query()
            ->where('school_class_id', $teachingAssignment->school_class_id)
            ->where(
                'academic_year_id',
                $teachingAssignment->schoolClass->academic_year_id
            )
            ->where('status', 'active')
            ->pluck('id')
            ->all();

        foreach ($validated['students'] as $student) {

            abort_unless(
                in_array(
                    $student['student_academic_year_id'],
                    $studentAcademicYearIds
                ),
                403
            );
        }


        /*
    |--------------------------------------------------------------------------
    | Cegah Duplikasi Absensi
    |--------------------------------------------------------------------------
    */

        $alreadyExists = Attendance::query()
            ->where(
                'teaching_assignment_id',
                $teachingAssignment->id
            )
            ->whereDate(
                'date',
                $validated['date']
            )
            ->where(
                'meeting_number',
                $validated['meeting_number']
            )
            ->exists();

        if ($alreadyExists) {
            return response()->json([
                'message' => 'Absensi untuk tanggal dan pertemuan tersebut sudah tersedia.',
            ], 422);
        }


        /*
    |--------------------------------------------------------------------------
    | Simpan Absensi
    |--------------------------------------------------------------------------
    */

        DB::transaction(function () use (
            $validated,
            $teachingAssignment,
            $request
        ) {

            $attendance = Attendance::create([
                'sync_id' => (string) \Illuminate\Support\Str::uuid(),
                'organization_id' => $teachingAssignment->organization_id,
                'teaching_assignment_id' => $teachingAssignment->id,
                'date' => $validated['date'],
                'meeting_number' => $validated['meeting_number'],
                'notes' => $validated['notes'] ?? null,
                'created_by' => $request->user()->id,
            ]);


            foreach ($validated['students'] as $student) {

                AttendanceDetail::create([
                    'attendance_id' => $attendance->id,
                    'student_academic_year_id' =>
                    $student['student_academic_year_id'],
                    'status' => $student['status'],
                    'notes' => $student['notes'] ?? null,
                ]);
            }
        });


        return redirect()
            ->route('guru.attendance.index', [
                'academic_year_id' => $request->academic_year_id,
                'school_class_id' => $request->school_class_id,
                'subject_id' => $request->subject_id,
            ])
            ->with('success', 'Absensi siswa berhasil disimpan.');
    }

    /**
     * Menampilkan data absensi untuk diedit.
     */
    public function edit(
        Request $request,
        Attendance $attendance
    ) {
        $teacher = $request->user()->teacher;

        abort_unless($teacher, 403);

        $attendance->load([
            'teachingAssignment.schoolClass',
            'teachingAssignment.subject',
            'teachingAssignment.organization',
            'details.studentAcademicYear.student',
        ]);

        /*
    |--------------------------------------------------------------------------
    | Pastikan absensi milik Guru yang login
    |--------------------------------------------------------------------------
    */

        abort_unless(
            $attendance->teachingAssignment &&
                $attendance->teachingAssignment->teacher_id === $teacher->id,
            403
        );

        /*
    |--------------------------------------------------------------------------
    | Batas waktu 6 jam
    |--------------------------------------------------------------------------
    */

        abort_unless(
            $attendance->created_at->gte(now()->subHours(6)),
            403,
            'Absensi hanya dapat diedit dalam waktu 6 jam setelah disimpan.'
        );

        return response()->json([
            'attendance' => [
                'id' => $attendance->id,
                'date' => $attendance->date->format('Y-m-d'),
                'meeting_number' => $attendance->meeting_number,
                'notes' => $attendance->notes,
                'class_name' =>
                $attendance->teachingAssignment->schoolClass->name,
                'subject_name' =>
                $attendance->teachingAssignment->subject->name,
                'organization_name' =>
                $attendance->teachingAssignment->organization->name,
                'students' => $attendance->details->map(function ($detail) {
                    return [
                        'id' => $detail->student_academic_year_id,
                        'nis' => $detail->studentAcademicYear->student->nis,
                        'name' => $detail->studentAcademicYear->student->name,
                        'status' => $detail->status,
                        'notes' => $detail->notes,
                    ];
                })->values(),
            ],
        ]);
    }

    /**
     * Memperbarui absensi siswa.
     */
    public function update(
        Request $request,
        Attendance $attendance
    ) {
        $teacher = $request->user()->teacher;

        abort_unless($teacher, 403);

        $attendance->load('teachingAssignment.schoolClass');

        /*
    |--------------------------------------------------------------------------
    | Pastikan milik Guru
    |--------------------------------------------------------------------------
    */

        abort_unless(
            $attendance->teachingAssignment &&
                $attendance->teachingAssignment->teacher_id === $teacher->id,
            403
        );

        /*
    |--------------------------------------------------------------------------
    | Batas 6 jam
    |--------------------------------------------------------------------------
    */

        abort_unless(
            $attendance->created_at->gte(now()->subHours(6)),
            403,
            'Absensi hanya dapat diedit dalam waktu 6 jam setelah disimpan.'
        );

        /*
    |--------------------------------------------------------------------------
    | Validasi
    |--------------------------------------------------------------------------
    */

        $validated = $request->validate([
            'date' => [
                'required',
                'date',
            ],

            'meeting_number' => [
                'required',
                'integer',
                'min:1',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'students' => [
                'required',
                'array',
                'min:1',
            ],

            'students.*.student_academic_year_id' => [
                'required',
                'integer',
                'exists:student_academic_years,id',
            ],

            'students.*.status' => [
                'required',
                Rule::in([
                    'present',
                    'sick',
                    'permission',
                    'absent',
                ]),
            ],

            'students.*.notes' => [
                'nullable',
                'string',
                'max:500',
            ],
        ]);

        /*
    |--------------------------------------------------------------------------
    | Cegah duplikasi
    |--------------------------------------------------------------------------
    */

        $alreadyExists = Attendance::query()
            ->where(
                'teaching_assignment_id',
                $attendance->teaching_assignment_id
            )
            ->whereDate(
                'date',
                $validated['date']
            )
            ->where(
                'meeting_number',
                $validated['meeting_number']
            )
            ->where(
                'id',
                '!=',
                $attendance->id
            )
            ->exists();

        if ($alreadyExists) {
            return response()->json([
                'message' =>
                'Absensi untuk tanggal dan pertemuan tersebut sudah tersedia.',
            ], 422);
        }

        /*
    |--------------------------------------------------------------------------
    | Pastikan siswa tetap berasal dari kelas yang benar
    |--------------------------------------------------------------------------
    */

        $studentAcademicYearIds = StudentAcademicYear::query()
            ->where(
                'school_class_id',
                $attendance->teachingAssignment->school_class_id
            )
            ->where(
                'academic_year_id',
                $attendance->teachingAssignment
                    ->schoolClass
                    ->academic_year_id
            )
            ->where('status', 'active')
            ->pluck('id')
            ->all();

        foreach ($validated['students'] as $student) {

            abort_unless(
                in_array(
                    $student['student_academic_year_id'],
                    $studentAcademicYearIds
                ),
                403
            );
        }

        /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

        DB::transaction(function () use (
            $attendance,
            $validated
        ) {

            $attendance->update([
                'date' => $validated['date'],
                'meeting_number' => $validated['meeting_number'],
                'notes' => $validated['notes'] ?? null,
            ]);

            $attendance->details()->delete();

            foreach ($validated['students'] as $student) {

                AttendanceDetail::create([
                    'attendance_id' => $attendance->id,
                    'student_academic_year_id' =>
                    $student['student_academic_year_id'],
                    'status' => $student['status'],
                    'notes' => $student['notes'] ?? null,
                ]);
            }
        });

        return response()->json([
            'message' => 'Absensi siswa berhasil diperbarui.',
        ]);
    }

    /**
     * Menghapus absensi siswa.
     */
    public function destroy(
        Request $request,
        Attendance $attendance
    ) {
        $teacher = $request->user()->teacher;

        abort_unless($teacher, 403);

        $attendance->load('teachingAssignment');

        /*
    |--------------------------------------------------------------------------
    | Pastikan milik Guru
    |--------------------------------------------------------------------------
    */

        abort_unless(
            $attendance->teachingAssignment &&
                $attendance->teachingAssignment->teacher_id === $teacher->id,
            403
        );

        /*
    |--------------------------------------------------------------------------
    | Batas 6 jam
    |--------------------------------------------------------------------------
    */

        abort_unless(
            $attendance->created_at->gte(now()->subHours(6)),
            403,
            'Absensi hanya dapat dihapus dalam waktu 6 jam setelah disimpan.'
        );

        DB::transaction(function () use ($attendance) {

            $attendance->details()->delete();

            $attendance->delete();
        });

        return redirect()
            ->route('guru.attendance.index')
            ->with(
                'success',
                'Absensi siswa berhasil dihapus.'
            );
    }
}
