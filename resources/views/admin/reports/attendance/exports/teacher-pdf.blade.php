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

 .kop {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 3px;
}

.kop td {
    border: none;
    vertical-align: middle;
}

.logo-cell {
    width: 25%;
    text-align: left;
}

.logo {
    width: 60px;
    height: 60px;
    object-fit: contain;
}

.kop-text {
    width: 50%;
    text-align: center;
    font-size: 11px;
    line-height: 1.35;
}

.kop-text-right {
    width: 25%;
    text-align: right;
    font-size: 9px;
    line-height: 1.35;
}

.organization-name {
    font-size: 14px;
    font-weight: bold;
    text-transform: uppercase;
}

.kop-line {
    border-top: 2px solid #000;
    margin-bottom: 8px;
}
    </style>

</head>

<body>

    {{-- ==========================================================
         KOP LAPORAN
         ========================================================== --}}

    <table class="kop">

        <tr>

            {{-- LOGO --}}
            <td class="logo-cell">

                @if ($organization?->logo_path)
                    <img src="{{ public_path('storage/' . $organization->logo_path) }}" class="logo">
                @elseif ($induk?->logo_path)
                    <img src="{{ public_path('storage/' . $induk->logo_path) }}" class="logo">
                @endif

            </td>


            {{-- IDENTITAS ORGANISASI --}}
            <td class="kop-text">

                <div class="organization-name">
                    {{ $organization?->name ?? ($induk?->name ?? 'Perkumpulan Mamba\'ul Ulum Bedanten') }}
                </div>

                @if ($organization?->address ?? $induk?->address)
                    <div>
                        {{ $organization?->address ?? $induk?->address }}
                    </div>
                @endif

            </td>


            {{-- KONTAK --}}
            <td class="kop-text-right">

                @if ($organization?->phone ?? $induk?->phone)
                    <div>
                        Telp. {{ $organization?->phone ?? $induk?->phone }}
                    </div>
                @endif

                @if ($organization?->email ?? $induk?->email)
                    <div>
                        Email: {{ $organization?->email ?? $induk?->email }}
                    </div>
                @endif

                @if ($organization?->website ?? $induk?->website)
                    <div>
                        {{ $organization?->website ?? $induk?->website }}
                    </div>
                @endif

            </td>

        </tr>

    </table>

    <div class="kop-line"></div>

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

                            $cellStart = \Carbon\Carbon::createFromFormat('H:i', $hour);

                            $cellEnd = $cellStart->copy()->addMinutes($teacherAttendanceInterval);

                            $cellAttendances = $weeklyAttendances->filter(function ($attendance) use (
                                $cellStart,
                                $cellEnd,
                                $dayNumber,
                            ) {
                                $attendanceTime = \Carbon\Carbon::parse($attendance->created_at);

                                return $attendance->date->dayOfWeekIso === $dayNumber &&
                                    $attendanceTime->format('H:i') >= $cellStart->format('H:i') &&
                                    $attendanceTime->format('H:i') < $cellEnd->format('H:i');
                            });

                        @endphp


                        <td>

                            @forelse ($cellAttendances
                                as $attendance)
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

                                @if (!$loop->last)
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
