<style>
    .student-attendance-card {
        display: block;
        width: 100%;
        border: 0;
        border-radius: 8px;
        padding: 7px 9px;
        margin-bottom: 4px;
        text-align: left;
        cursor: pointer;
        transition: all 0.15s ease;
        border-left: 4px solid transparent;
        box-shadow: none;
    }

    .student-attendance-card:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.12);
    }

    .student-attendance-card-body {
        line-height: 1.2;
    }

    .student-attendance-subject {
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 3px;
        color: #1f2937;
    }

    .student-attendance-meeting {
        font-size: 11px;
        color: #6b7280;
        margin-bottom: 5px;
    }

    .student-attendance-card .badge {
        font-size: 10px;
        font-weight: 500;
        padding: 3px 7px;
        border-radius: 10px;
    }


    /*
    |--------------------------------------------------------------------------
    | Hadir
    |--------------------------------------------------------------------------
    */

    .student-attendance-hadir {
        background: #ecfdf3;
        border-left-color: #22c55e;
    }


    /*
    |--------------------------------------------------------------------------
    | Izin
    |--------------------------------------------------------------------------
    */

    .student-attendance-izin {
        background: #fffbeb;
        border-left-color: #f59e0b;
    }


    /*
    |--------------------------------------------------------------------------
    | Sakit
    |--------------------------------------------------------------------------
    */

    .student-attendance-sakit {
        background: #eff6ff;
        border-left-color: #3b82f6;
    }


    /*
    |--------------------------------------------------------------------------
    | Alpa
    |--------------------------------------------------------------------------
    */

    .student-attendance-alpa {
        background: #fef2f2;
        border-left-color: #ef4444;
    }


    /*
    |--------------------------------------------------------------------------
    | Default
    |--------------------------------------------------------------------------
    */

    .student-attendance-default {
        background: #f3f4f6;
        border-left-color: #6b7280;
    }
</style>

<div class="table-responsive">

    <table class="table table-bordered mb-0" style="min-width: 1000px;">

        <thead>
            <tr>
                <th style="width: 90px;">
                    Jam
                </th>

                @php
                    $days = [
                        1 => 'Senin',
                        2 => 'Selasa',
                        3 => 'Rabu',
                        4 => 'Kamis',
                        5 => 'Jumat',
                        6 => 'Sabtu',
                        7 => 'Minggu',
                    ];
                @endphp

                @foreach ($days as $dayNumber => $dayName)
                    @php
                        $date = $weekStart
                            ->copy()
                            ->startOfWeek()
                            ->addDays($dayNumber - 1);
                    @endphp

                    <th class="text-center">

                        <div>
                            {{ $dayName }}
                        </div>

                        <small class="text-muted">
                            {{ $date->format('d/m') }}
                        </small>

                    </th>
                @endforeach

            </tr>
        </thead>

        <tbody>

            @php
                $current = $startHour * 60;
                $end = $endHour * 60;
            @endphp

            @while ($current < $end)

                @php
                    $hour = intdiv($current, 60);
                    $minute = $current % 60;

                    $timeLabel = sprintf('%02d:%02d', $hour, $minute);
                @endphp

                <tr>

                    <td class="text-center align-middle bg-light">

                        <small class="font-weight-bold">
                            {{ $timeLabel }}
                        </small>

                    </td>

                    @foreach ($days as $dayNumber => $dayName)
                        @php
                            $date = $weekStart
                                ->copy()
                                ->startOfWeek()
                                ->addDays($dayNumber - 1);

                            /*
                            |--------------------------------------------------------------------------
                            | Absensi pada hari ini
                            |--------------------------------------------------------------------------
                            */
                            $items = $weeklyAttendances->filter(function ($detail) use ($date) {
                                return optional($detail->attendance?->date)->isSameDay($date);
                            });
                        @endphp

                        <td class="align-top" style="height: 80px; min-width: 130px;">

                            @foreach ($items as $detail)
                                @php
                                    $attendance = $detail->attendance;
                                    $assignment = $attendance?->teachingAssignment;

                                    /*
                                    |--------------------------------------------------------------------------
                                    | Sementara:
                                    | posisi waktu menggunakan created_at,
                                    | sama seperti Teacher Attendance.
                                    |--------------------------------------------------------------------------
                                    */
                                    $attendanceTime = $attendance?->created_at;

                                    $attendanceHour = $attendanceTime ? $attendanceTime->hour : null;

                                    $attendanceMinute = $attendanceTime ? $attendanceTime->minute : null;

                                    $attendanceTotalMinute =
                                        $attendanceHour !== null ? $attendanceHour * 60 + $attendanceMinute : null;

                                    $cellStart = $current;
                                    $cellEnd = $current + $interval;

                                    $isInCell =
                                        $attendanceTotalMinute !== null &&
                                        $attendanceTotalMinute >= $cellStart &&
                                        $attendanceTotalMinute < $cellEnd;
                                @endphp

                                @if ($isInCell)
                                    @php
                                        $status = strtolower($detail->status);

                                        $statusClass = match ($status) {
                                            'present' => 'student-attendance-hadir',
                                            'permission' => 'student-attendance-izin',
                                            'sick' => 'student-attendance-sakit',
                                            'absent' => 'student-attendance-alpa',
                                            default => 'student-attendance-default',
                                        };

                                        $statusBadgeClass = match ($status) {
                                            'present' => 'bg-success',
                                            'permission' => 'bg-warning text-dark',
                                            'sick' => 'bg-info',
                                            'absent' => 'bg-danger',
                                            default => 'bg-secondary',
                                        };
                                    @endphp

                                    <button type="button" class="student-attendance-card {{ $statusClass }}"
                                        data-bs-toggle="modal" data-bs-target="#studentAttendanceDetailModal"
                                        data-student="{{ $detail->studentAcademicYear?->student?->name ?? '-' }}"
                                        data-date="{{ $attendance?->date?->translatedFormat('d F Y') ?? '-' }}"
                                        data-meeting="{{ $attendance->meeting_number ?? '-' }}"
                                        data-subject="{{ $assignment?->subject?->name ?? '-' }}"
                                        data-teacher="{{ $assignment?->teacher?->name ?? '-' }}"
                                        data-organization="{{ $assignment?->organization?->name ?? '-' }}"
                                        data-class="{{ $assignment?->schoolClass?->name ?? '-' }}"
                                        data-status="{{ $detail->status ?? '-' }}"
                                        data-notes="{{ $detail->notes ?? '' }}">

                                        <div class="student-attendance-card-body">

                                            {{-- Mata Pelajaran --}}
                                            <div class="student-attendance-subject">
                                                {{ $assignment?->subject?->name ?? '-' }}
                                            </div>

                                            {{-- Pertemuan --}}
                                            <div class="student-attendance-meeting">
                                                Pertemuan {{ $attendance->meeting_number ?? '-' }}
                                            </div>

                                            {{-- Status --}}
                                            <span class="badge {{ $statusBadgeClass }}">
                                                {{ ucfirst($detail->status) }}
                                            </span>

                                        </div>

                                    </button>
                                @endif
                            @endforeach

                        </td>
                    @endforeach

                </tr>

                @php
                    $current += $interval;
                @endphp

            @endwhile

        </tbody>

    </table>

</div>
