<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">

    <title>Laporan Absensi Guru</title>

    <style>
        @page {
            size: A4 landscape;
            margin: 10mm;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 8px;
        }

        h2 {
            margin: 0 0 4px 0;
            text-align: center;
            font-size: 14px;
        }

        .info {
            margin-bottom: 8px;
            font-size: 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 3px;
            vertical-align: top;
        }

        th {
            text-align: center;
            font-weight: bold;
        }

        .time {
            width: 55px;
            text-align: center;
            vertical-align: middle;
            font-weight: bold;
        }

        .day {
            text-align: center;
            width: auto;
        }

        .cell {
            min-height: 25px;
        }

        .class {
            font-weight: bold;
        }

        .subject {
            font-size: 7px;
        }

        .teacher {
            font-size: 7px;
        }

        .input-time {
            font-size: 6px;
        }

        .empty {
            text-align: center;
            color: #777;
        }
    </style>
</head>

<body>

    <h2>
        LAPORAN ABSENSI GURU
    </h2>

    <div class="info">

        <strong>Periode:</strong>
        {{ $weekStart->translatedFormat('d F Y') }}
        -
        {{ $weekEnd->translatedFormat('d F Y') }}

        @if ($academicYear)
            &nbsp;&nbsp;|&nbsp;&nbsp;

            <strong>Tahun Ajaran:</strong>
            {{ $academicYear->name }}
        @endif

        @if ($organization)
            &nbsp;&nbsp;|&nbsp;&nbsp;

            <strong>Unit:</strong>
            {{ $organization->name }}
        @endif

        @if ($schoolClass)
            &nbsp;&nbsp;|&nbsp;&nbsp;

            <strong>Kelas:</strong>
            {{ $schoolClass->name }}
        @endif

        @if ($subject)
            &nbsp;&nbsp;|&nbsp;&nbsp;

            <strong>Mata Pelajaran:</strong>
            {{ $subject->name }}
        @endif

    </div>


    <table>

        <thead>
            <tr>

                <th class="time">
                    Jam
                </th>

                @foreach ([
                    1 => 'Senin',
                    2 => 'Selasa',
                    3 => 'Rabu',
                    4 => 'Kamis',
                    5 => 'Jumat',
                    6 => 'Sabtu',
                    7 => 'Minggu',
                ] as $dayNumber => $dayName)

                    <th class="day">
                        {{ $dayName }}
                    </th>

                @endforeach

            </tr>
        </thead>


        <tbody>

            @foreach ($teacherAttendanceHours as $hour)

                <tr>

                    <td class="time">
                        {{ $hour }}
                    </td>


                    @foreach (range(1, 7) as $dayNumber)

                        @php

                            $cellStart =
                                \Carbon\Carbon::createFromFormat(
                                    'H:i',
                                    $hour
                                );

                            $cellEnd =
                                $cellStart
                                    ->copy()
                                    ->addMinutes(
                                        $teacherAttendanceInterval
                                    );

                            $cellAttendances =
                                $weeklyAttendances
                                    ->filter(
                                        function ($attendance) use (
                                            $cellStart,
                                            $cellEnd,
                                            $dayNumber
                                        ) {

                                            $attendanceTime =
                                                \Carbon\Carbon::parse(
                                                    $attendance->created_at
                                                );

                                            return
                                                $attendance
                                                    ->date
                                                    ->dayOfWeekIso
                                                    === $dayNumber

                                                &&

                                                $attendanceTime
                                                    ->format('H:i')
                                                    >=
                                                $cellStart
                                                    ->format('H:i')

                                                &&

                                                $attendanceTime
                                                    ->format('H:i')
                                                    <
                                                $cellEnd
                                                    ->format('H:i');
                                        }
                                    );

                        @endphp


                        <td>

                            @forelse (
                                $cellAttendances
                                as $attendance
                            )

                                <div class="cell">

                                    <div class="class">
                                        {{ $attendance->teachingAssignment->schoolClass->name }}
                                    </div>

                                    <div class="subject">
                                        {{ $attendance->teachingAssignment->subject->name }}
                                    </div>

                                    <div class="teacher">
                                        {{ $attendance->teachingAssignment->teacher->name }}
                                    </div>

                                    <div class="input-time">
                                        Input:
                                        {{ $attendance->created_at->format('H:i') }}
                                    </div>

                                </div>

                                @if (! $loop->last)
                                    <hr>
                                @endif

                            @empty

                                <div class="empty">
                                    -
                                </div>

                            @endforelse

                        </td>

                    @endforeach

                </tr>

            @endforeach

        </tbody>

    </table>

</body>
</html>
