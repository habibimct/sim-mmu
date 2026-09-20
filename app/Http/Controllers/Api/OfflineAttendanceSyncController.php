<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceDetail;
use App\Models\StudentAcademicYear;
use App\Models\TeachingAssignment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class OfflineAttendanceSyncController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'sync_id' => ['required', 'uuid'],
            'operation' => ['required', 'in:create'],
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

        $user = $request->user();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'User belum terautentikasi.',
            ], 401);
        }

        $teacher = $user->teacher;

        if (! $teacher) {
            return response()->json([
                'success' => false,
                'message' => 'Akun ini belum terhubung dengan data Guru.',
            ], 403);
        }

        /*
        |--------------------------------------------------------------------------
        | Idempotency berdasarkan sync_id
        |--------------------------------------------------------------------------
        */

        $existing = Attendance::where(
            'sync_id',
            $validated['sync_id']
        )->first();

        if ($existing) {
            return response()->json([
                'success' => true,
                'message' => 'Absensi offline sudah pernah disinkronkan.',
                'ack' => [
                    'sync_id' => $existing->sync_id,
                    'status' => 'duplicate',
                    'attendance_id' => $existing->id,
                ],
                'server_time' => now()->toIso8601String(),
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Teaching Assignment milik Guru
        |--------------------------------------------------------------------------
        */

        $teachingAssignment = TeachingAssignment::query()
            ->with('schoolClass')
            ->where('id', $validated['teaching_assignment_id'])
            ->where('teacher_id', $teacher->id)
            ->where('is_active', true)
            ->first();

        if (! $teachingAssignment) {
            return response()->json([
                'success' => false,
                'message' => 'Teaching assignment tidak valid atau bukan milik Anda.',
            ], 403);
        }

        /*
        |--------------------------------------------------------------------------
        | Pastikan kelas masih aktif dan bukan alumni
        |--------------------------------------------------------------------------
        */

        if (
            ! $teachingAssignment->schoolClass ||
            ! $teachingAssignment->schoolClass->is_active ||
            $teachingAssignment->schoolClass->is_alumni
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Kelas sudah tidak aktif atau merupakan kelas alumni.',
            ], 403);
        }

        /*
        |--------------------------------------------------------------------------
        | Pastikan siswa berasal dari kelas dan tahun akademik yang benar
        |--------------------------------------------------------------------------
        */

        $studentAcademicYearIds = StudentAcademicYear::query()
            ->where(
                'school_class_id',
                $teachingAssignment->school_class_id
            )
            ->where(
                'academic_year_id',
                $teachingAssignment->schoolClass->academic_year_id
            )
            ->where('status', 'active')
            ->pluck('id')
            ->all();

        foreach ($validated['students'] as $student) {
            if (
                ! in_array(
                    $student['student_academic_year_id'],
                    $studentAcademicYearIds
                )
            ) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terdapat siswa yang bukan bagian dari kelas teaching assignment tersebut.',
                ], 403);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Cegah duplikasi berdasarkan teaching assignment,
        | tanggal, dan nomor pertemuan.
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
            ->first();

        if ($alreadyExists) {
            /*
    |--------------------------------------------------------------------------
    | Request yang sama dikirim ulang
    |--------------------------------------------------------------------------
    |
    | sync_id sama berarti ini adalah retry dari transaksi offline
    | yang sebelumnya sudah berhasil dibuat di server.
    |
    */

            if ($alreadyExists->sync_id === $validated['sync_id']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Absensi offline sudah pernah disinkronkan.',
                    'ack' => [
                        'sync_id' => $alreadyExists->sync_id,
                        'status' => 'duplicate',
                        'attendance_id' => $alreadyExists->id,
                    ],
                    'server_time' => now()->toIso8601String(),
                ]);
            }

            /*
    |--------------------------------------------------------------------------
    | Data berbeda tetapi menggunakan assignment,
    | tanggal, dan pertemuan yang sama
    |--------------------------------------------------------------------------
    */

            return response()->json([
                'success' => false,
                'message' => 'Absensi untuk tanggal dan nomor pertemuan tersebut sudah tersedia dengan data lain.',
                'conflict' => true,
                'ack' => [
                    'sync_id' => $validated['sync_id'],
                    'status' => 'conflict',
                    'attendance_id' => $alreadyExists->id,
                ],
                'server_time' => now()->toIso8601String(),
            ], 409);
        }

        /*
        |--------------------------------------------------------------------------
        | Simpan Attendance + AttendanceDetail
        |--------------------------------------------------------------------------
        */

        $attendance = DB::transaction(function () use (
            $validated,
            $teachingAssignment,
            $user
        ) {
            $attendance = Attendance::create([
                'sync_id' => $validated['sync_id'],
                'organization_id' => $teachingAssignment->organization_id,
                'teaching_assignment_id' => $teachingAssignment->id,
                'date' => $validated['date'],
                'meeting_number' => $validated['meeting_number'],
                'notes' => $validated['notes'] ?? null,
                'created_by' => $user->id,
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

            return $attendance;
        });

        return response()->json([
            'success' => true,
            'message' => 'Absensi offline berhasil disinkronkan.',
            'ack' => [
                'sync_id' => $attendance->sync_id,
                'status' => 'acknowledged',
                'attendance_id' => $attendance->id,
            ],
            'server_time' => now()->toIso8601String(),
        ]);
    }
}
