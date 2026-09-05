<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\TeachingAssignment;
use App\Models\AcademicYear;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TeachingAssignmentController extends Controller
{
    /**
     * Daftar penugasan mengajar.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | Organisasi yang boleh dikelola
        |--------------------------------------------------------------------------
        */

        $organizationIds = $user
            ->organizations()
            ->where('is_active', true)
            ->pluck('organizations.id');


        /*
        |--------------------------------------------------------------------------
        | Query penugasan
        |--------------------------------------------------------------------------
        */

        $query = TeachingAssignment::query()
            ->with([
                'teacher',
                'schoolClass.academicYear',
                'subject',
            ])
            ->whereIn(
                'organization_id',
                $organizationIds
            );


        /*
        |--------------------------------------------------------------------------
        | Filter pencarian
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search = trim(
                $request->search
            );

            $query->where(function ($q) use ($search) {

                $q->whereHas(
                    'teacher',
                    function ($teacherQuery) use ($search) {

                        $teacherQuery
                            ->where(
                                'name',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'nik',
                                'like',
                                "%{$search}%"
                            );
                    }
                )
                    ->orWhereHas(
                        'schoolClass',
                        function ($classQuery) use ($search) {

                            $classQuery->where(
                                'name',
                                'like',
                                "%{$search}%"
                            );
                        }
                    )
                    ->orWhereHas(
                        'subject',
                        function ($subjectQuery) use ($search) {

                            $subjectQuery
                                ->where(
                                    'name',
                                    'like',
                                    "%{$search}%"
                                )
                                ->orWhere(
                                    'code',
                                    'like',
                                    "%{$search}%"
                                );
                        }
                    );
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Filter status
        |--------------------------------------------------------------------------
        */
        
        if ($request->filled('academic_year_id')) {

            $query->whereHas('schoolClass', function ($q) use ($request) {

                $q->where(
                    'academic_year_id',
                    $request->academic_year_id
                );
            });
        }


        /*
        |--------------------------------------------------------------------------
        | Filter status
        |--------------------------------------------------------------------------
        */

        if ($request->status === 'active') {

            $query->where(
                'is_active',
                true
            );
        } elseif ($request->status === 'inactive') {

            $query->where(
                'is_active',
                false
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Filter kelas
        |--------------------------------------------------------------------------
        */

        if ($request->filled('school_class_id')) {

            $query->where(
                'school_class_id',
                $request->school_class_id
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Filter mata pelajaran
        |--------------------------------------------------------------------------
        */

        if ($request->filled('subject_id')) {

            $query->where(
                'subject_id',
                $request->subject_id
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Data penugasan
        |--------------------------------------------------------------------------
        */

        $teachingAssignments = $query
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();



        /*
        |--------------------------------------------------------------------------
        | Kelompok penugasan untuk modal Edit
        |--------------------------------------------------------------------------
        */

        $assignmentGroupIds =
            $teachingAssignments
            ->getCollection()
            ->map(function ($assignment) {

                return [
                    'teacher_id' =>
                    $assignment->teacher_id,

                    'subject_id' =>
                    $assignment->subject_id,

                    'academic_year_id' =>
                    $assignment->schoolClass->academic_year_id,
                ];
            })
            ->unique(function ($item) {

                return implode('|', $item);
            })
            ->values();


        $assignmentGroups = collect();


        foreach ($assignmentGroupIds as $group) {

            $classIds = TeachingAssignment::query()
                ->where(
                    'teacher_id',
                    $group['teacher_id']
                )
                ->where(
                    'subject_id',
                    $group['subject_id']
                )
                ->whereHas(
                    'schoolClass',
                    function ($query) use ($group) {

                        $query->where(
                            'academic_year_id',
                            $group['academic_year_id']
                        );
                    }
                )
                ->pluck('school_class_id');


            $key = implode('|', [
                $group['teacher_id'],
                $group['subject_id'],
                $group['academic_year_id'],
            ]);


            $assignmentGroups->put(
                $key,
                $classIds
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Data untuk modal
        |--------------------------------------------------------------------------
        |
        | Semua data dibatasi ke organisasi user.
        |
        */

        $teachers = Teacher::query()
            ->whereHas(
                'organizations',
                function ($query) use ($organizationIds) {

                    $query->whereIn(
                        'organizations.id',
                        $organizationIds
                    );
                }
            )
            ->where('is_active', true)
            ->orderBy('name')
            ->get();


        $schoolClasses = SchoolClass::query()
            ->whereIn(
                'organization_id',
                $organizationIds
            )
            ->where('is_active', true)
            ->where('is_alumni', false)
            ->with('academicYear')
            ->orderBy('level')
            ->orderBy('name')
            ->get();


        $subjects = Subject::query()
            ->whereIn(
                'organization_id',
                $organizationIds
            )
            ->where('is_active', true)
            ->orderBy('name')
            ->get();


        $academicYears = AcademicYear::query()
            ->whereHas('schoolClasses', function ($query) use ($organizationIds) {
                $query->whereIn(
                    'organization_id',
                    $organizationIds
                );
            })
            ->orderByDesc('start_date')
            ->get();


        return view(
            'admin.teaching-assignments.index',
            compact(
                'teachingAssignments',
                'teachers',
                'schoolClasses',
                'subjects',
                'academicYears',
                'assignmentGroups'
            )
        );
    }


    /**
     * Menyimpan penugasan mengajar.
     */
    public function store(
        Request $request
    ): RedirectResponse {

        $user = $request->user();


        /*
    |--------------------------------------------------------------------------
    | Organisasi user
    |--------------------------------------------------------------------------
    */

        $organizationIds = $user
            ->organizations()
            ->where('is_active', true)
            ->pluck('organizations.id');


        /*
    |--------------------------------------------------------------------------
    | Validasi input
    |--------------------------------------------------------------------------
    */

        $validated = $request->validate([

            'teacher_id' => [
                'required',
                'integer',
                'exists:teachers,id',
            ],

            'school_class_ids' => [
                'required',
                'array',
                'min:1',
            ],

            'school_class_ids.*' => [
                'integer',
                'exists:school_classes,id',
            ],

            'subject_id' => [
                'required',
                'integer',
                'exists:subjects,id',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],

        ]);


        /*
    |--------------------------------------------------------------------------
    | Pastikan Guru berada di organisasi user
    |--------------------------------------------------------------------------
    */

        $teacherAllowed = Teacher::query()
            ->whereKey($validated['teacher_id'])
            ->whereHas(
                'organizations',
                function ($query) use ($organizationIds) {

                    $query->whereIn(
                        'organizations.id',
                        $organizationIds
                    );
                }
            )
            ->exists();


        abort_unless(
            $teacherAllowed,
            403
        );


        /*
    |--------------------------------------------------------------------------
    | Ambil semua kelas yang dipilih
    |--------------------------------------------------------------------------
    */

        $schoolClasses = SchoolClass::query()
            ->whereIn(
                'id',
                $validated['school_class_ids']
            )
            ->whereIn(
                'organization_id',
                $organizationIds
            )
            ->get();


        /*
    |--------------------------------------------------------------------------
    | Pastikan semua kelas benar-benar berada dalam scope user
    |--------------------------------------------------------------------------
    */

        abort_unless(
            $schoolClasses->count()
                === count($validated['school_class_ids']),
            403
        );


        /*
    |--------------------------------------------------------------------------
    | Pastikan Mata Pelajaran berada di organisasi user
    |--------------------------------------------------------------------------
    */

        $subject = Subject::query()
            ->whereKey($validated['subject_id'])
            ->whereIn(
                'organization_id',
                $organizationIds
            )
            ->first();


        abort_unless(
            $subject,
            403
        );


        /*
    |--------------------------------------------------------------------------
    | Semua kelas harus berada pada organisasi yang sama
    | dengan mata pelajaran.
    |--------------------------------------------------------------------------
    */

        foreach ($schoolClasses as $schoolClass) {

            abort_unless(
                $subject->organization_id
                    === $schoolClass->organization_id,
                422
            );
        }


        /*
    |--------------------------------------------------------------------------
    | Cek duplikasi
    |--------------------------------------------------------------------------
    */

        $existingClassIds = TeachingAssignment::query()
            ->where(
                'teacher_id',
                $validated['teacher_id']
            )
            ->where(
                'subject_id',
                $validated['subject_id']
            )
            ->whereIn(
                'school_class_id',
                $validated['school_class_ids']
            )
            ->pluck('school_class_id');


        /*
    |--------------------------------------------------------------------------
    | Jika ada duplikasi
    |--------------------------------------------------------------------------
    */

        if ($existingClassIds->isNotEmpty()) {

            return back()
                ->withErrors([
                    'teaching_assignment' =>
                    'Sebagian atau seluruh kelas yang dipilih sudah memiliki penugasan untuk guru dan mata pelajaran tersebut.',
                ])
                ->withInput()
                ->with(
                    'open_teaching_assignment_modal',
                    true
                );
        }


        /*
    |--------------------------------------------------------------------------
    | Simpan semua penugasan
    |--------------------------------------------------------------------------
    */

        foreach ($schoolClasses as $schoolClass) {

            TeachingAssignment::create([

                'organization_id' =>
                $schoolClass->organization_id,

                'teacher_id' =>
                $validated['teacher_id'],

                'school_class_id' =>
                $schoolClass->id,

                'subject_id' =>
                $validated['subject_id'],

                'is_active' =>
                $request->boolean(
                    'is_active',
                    true
                ),

            ]);
        }


        /*
    |--------------------------------------------------------------------------
    | Selesai
    |--------------------------------------------------------------------------
    */

        $jumlahKelas =
            $schoolClasses->count();


        return redirect()
            ->route('admin.teaching-assignments.index', array_filter([
                'search' => $request->redirect_search,
                'status' => $request->redirect_status,
                'school_class_id' => $request->redirect_school_class_id,
                'subject_id' => $request->redirect_subject_id,
            ]))
            ->with(
                'success',
                "Penugasan mengajar berhasil ditambahkan untuk {$jumlahKelas} kelas."
            );
    }


    /**
     * Memperbarui penugasan mengajar.
     */
    public function update(
        Request $request,
        TeachingAssignment $teachingAssignment
    ): RedirectResponse {

        $user = $request->user();

        /*
    |--------------------------------------------------------------------------
    | Organisasi user
    |--------------------------------------------------------------------------
    */

        $organizationIds = $user
            ->organizations()
            ->where('is_active', true)
            ->pluck('organizations.id');


        /*
    |--------------------------------------------------------------------------
    | Pastikan penugasan berada dalam scope user
    |--------------------------------------------------------------------------
    */

        abort_unless(
            $organizationIds->contains(
                $teachingAssignment->organization_id
            ),
            403
        );


        /*
    |--------------------------------------------------------------------------
    | Validasi input
    |--------------------------------------------------------------------------
    */

        $validated = $request->validate([

            'teacher_id' => [
                'required',
                'integer',
                'exists:teachers,id',
            ],

            'school_class_ids' => [
                'required',
                'array',
                'min:1',
            ],

            'school_class_ids.*' => [
                'integer',
                'exists:school_classes,id',
            ],

            'subject_id' => [
                'required',
                'integer',
                'exists:subjects,id',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],

        ]);


        /*
    |--------------------------------------------------------------------------
    | Ambil satu kelas yang dipilih
    |--------------------------------------------------------------------------
    */

        $schoolClassId = $validated['school_class_ids'][0];


        /*
    |--------------------------------------------------------------------------
    | Pastikan Guru berada di organisasi user
    |--------------------------------------------------------------------------
    */

        $teacherAllowed = Teacher::query()
            ->whereKey($validated['teacher_id'])
            ->whereHas(
                'organizations',
                function ($query) use ($organizationIds) {
                    $query->whereIn(
                        'organizations.id',
                        $organizationIds
                    );
                }
            )
            ->exists();

        abort_unless($teacherAllowed, 403);


        /*
    |--------------------------------------------------------------------------
    | Pastikan Kelas berada di organisasi user
    |--------------------------------------------------------------------------
    */

        $schoolClass = SchoolClass::query()
            ->whereKey($schoolClassId)
            ->whereIn(
                'organization_id',
                $organizationIds
            )
            ->first();

        abort_unless($schoolClass, 403);


        /*
    |--------------------------------------------------------------------------
    | Pastikan Mata Pelajaran berada di organisasi user
    |--------------------------------------------------------------------------
    */

        $subject = Subject::query()
            ->whereKey($validated['subject_id'])
            ->whereIn(
                'organization_id',
                $organizationIds
            )
            ->first();

        abort_unless($subject, 403);


        /*
    |--------------------------------------------------------------------------
    | Kelas dan Mata Pelajaran harus satu organisasi
    |--------------------------------------------------------------------------
    */

        abort_unless(
            $schoolClass->organization_id === $subject->organization_id,
            422
        );


        /*
    |--------------------------------------------------------------------------
    | UPDATE SATU PENUGASAN SAJA
    |--------------------------------------------------------------------------
    */

        $duplicate = TeachingAssignment::query()
            ->where('organization_id', $teachingAssignment->organization_id)
            ->where('teacher_id', $validated['teacher_id'])
            ->where('school_class_id', $schoolClassId)
            ->where('subject_id', $validated['subject_id'])
            ->where('id', '!=', $teachingAssignment->id)
            ->exists();

        if ($duplicate) {
            return redirect()
                ->route('admin.teaching-assignments.index')
                ->withErrors([
                    'subject_id' =>
                    'Penugasan Guru, Kelas, dan Mata Pelajaran tersebut sudah ada.'
                ])
                ->withInput()
                ->with('open_teaching_assignment_modal', true);
        }

        $teachingAssignment->update([

            'teacher_id' => $validated['teacher_id'],

            'school_class_id' => $schoolClassId,

            'subject_id' => $validated['subject_id'],

            'is_active' => $request->boolean('is_active'),

        ]);


        /*
    |--------------------------------------------------------------------------
    | Selesai
    |--------------------------------------------------------------------------
    */

        return redirect()
            ->route('admin.teaching-assignments.index', array_filter([
                'search' => $request->redirect_search,
                'status' => $request->redirect_status,
                'school_class_id' => $request->redirect_school_class_id,
                'subject_id' => $request->redirect_subject_id,
            ]))
            ->with(
                'success',
                'Penugasan mengajar berhasil diperbarui.'
            );
    }

    /**
     * Menghapus penugasan mengajar.
     */
    public function destroy(
        Request $request,
        TeachingAssignment $teachingAssignment
    ): RedirectResponse {

        $user = $request->user();


        $organizationIds = $user
            ->organizations()
            ->where('is_active', true)
            ->pluck('organizations.id');


        /*
        |--------------------------------------------------------------------------
        | Pastikan berada dalam scope organisasi
        |--------------------------------------------------------------------------
        */

        abort_unless(
            $organizationIds->contains(
                $teachingAssignment->organization_id
            ),
            403
        );


        $teachingAssignment->delete();


        return redirect()
            ->route(
                'admin.teaching-assignments.index'
            )
            ->with(
                'success',
                'Penugasan mengajar berhasil dihapus.'
            );
    }
}
