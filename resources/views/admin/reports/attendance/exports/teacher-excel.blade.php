<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">

    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px;
            vertical-align: top;
        }

        th {
            font-weight: bold;
            text-align: center;
        }

        .title {
            font-size: 16px;
            font-weight: bold;
        }

        .center {
            text-align: center;
        }

        .small {
            font-size: 10px;
        }
    </style>
</head>

<body>

<table>
    <tr>
        <td colspan="8" class="title">
            LAPORAN ABSENSI GURU
        </td>
    </tr>

    <tr>
        <td colspan="8">
            Periode:
            {{ $weekStart->translatedFormat('d F Y') }}
            -
            {{ $weekEnd->translatedFormat('d F Y') }}
        </td>
    </tr>

    @if ($academicYear)
        <tr>
            <td colspan="8">
                Tahun Ajaran:
                {{ $academicYear->name }}
            </td>
        </tr>
    @endif

    @if ($organization)
        <tr>
            <td colspan="8">
                Unit:
                {{ $organization->name }}
            </td>
        </tr>
    @endif

    @if ($schoolClass)
        <tr>
            <td colspan="8">
                Kelas:
                {{ $schoolClass->name }}
            </td>
        </tr>
    @endif

    @if ($subject)
        <tr>
            <td colspan="8">
                Mata Pelajaran:
                {{ $subject->name }}
            </td>
        </tr>
    @endif

    <tr>
        <td colspan="8"></td>
    </tr>

    <tr>
        <th style="width: 70px;">
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

            <th>
                {{ $dayName }}
            </th>

        @endforeach
    </tr>


    @foreach ($teacherAttendanceHours as $hour)

        <tr>

            <td class="center">
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

                        <strong>
                            {{ $attendance->teachingAssignment->schoolClass->name }}
                        </strong>

                        <br>

                        {{ $attendance->teachingAssignment->subject->name }}

                        <br>

                        {{ $attendance->teachingAssignment->teacher->name }}

                        <br>

                        <span class="small">
                            Input:
                            {{ $attendance->created_at->format('H:i') }}
                        </span>

                        @if (! $loop->last)
                            <hr>
                        @endif

                    @empty

                        -

                    @endforelse

                </td>

            @endforeach

        </tr>

    @endforeach

</table>

</body>
</html>
