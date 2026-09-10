<!DOCTYPE html>
<html>
<head>

    <meta charset="UTF-8">

    <title>
        Laporan Absensi Siswa
    </title>

    <style>

        @page {
            margin: 15px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 3px;
            text-align: center;
            vertical-align: middle;
        }

        th {
            font-weight: bold;
            background-color: #eeeeee;
        }

        .title {
            font-size: 15px;
            font-weight: bold;
        }

        .info {
            border: none;
            text-align: left;
            padding: 2px;
        }

        .text-left {
            text-align: left;
        }

    </style>

</head>

<body>


    {{-- ==========================================================
         JUDUL
         ========================================================== --}}

    <table>

        <tr>
            <td
                colspan="{{ $monthStart->daysInMonth + 7 }}"
                class="title"
            >
                LAPORAN ABSENSI SISWA
            </td>
        </tr>

        <tr>
            <td
                colspan="{{ $monthStart->daysInMonth + 7 }}"
                class="info"
            >
                Bulan:
                {{ $monthStart->translatedFormat('F Y') }}
            </td>
        </tr>

        <tr>
            <td
                colspan="{{ $monthStart->daysInMonth + 7 }}"
                class="info"
            >
                Tahun Ajaran:
                {{ $academicYear?->name ?? '-' }}
            </td>
        </tr>

        <tr>
            <td
                colspan="{{ $monthStart->daysInMonth + 7 }}"
                class="info"
            >
                Unit:
                {{ $organization?->name ?? 'Semua Unit' }}
            </td>
        </tr>

        <tr>
            <td
                colspan="{{ $monthStart->daysInMonth + 7 }}"
                class="info"
            >
                Kelas:
                {{ $schoolClass?->name ?? 'Semua Kelas' }}
            </td>
        </tr>

        <tr>
            <td
                colspan="{{ $monthStart->daysInMonth + 7 }}"
                class="info"
            >
                Mata Pelajaran:
                {{ $subject?->name ?? 'Semua Mata Pelajaran' }}
            </td>
        </tr>

    </table>


    <br>


    {{-- ==========================================================
         TABEL
         ========================================================== --}}

    <table>

        <thead>

            <tr>

                <th rowspan="2">
                    No
                </th>

                <th rowspan="2">
                    NIS
                </th>

                <th rowspan="2">
                    Nama Siswa
                </th>

                <th colspan="{{ $monthStart->daysInMonth }}">
                    Tanggal
                </th>

                <th colspan="4">
                    Rekap
                </th>

            </tr>


            <tr>

                @for (
                    $day = $monthStart->copy();
                    $day <= $monthEnd;
                    $day->addDay()
                )

                    <th>
                        {{ $day->format('d') }}
                    </th>

                @endfor


                <th>
                    H
                </th>

                <th>
                    S
                </th>

                <th>
                    I
                </th>

                <th>
                    A
                </th>

            </tr>

        </thead>


        <tbody>

            @foreach (
                $students as $index => $student
            )

                @php

                    $daily =
                        $dailyStatuses[$student->id]
                        ?? collect();


                    $present =
                        $daily
                        ->filter(
                            fn ($status) =>
                                $status === 'present'
                        )
                        ->count();


                    $sick =
                        $daily
                        ->filter(
                            fn ($status) =>
                                $status === 'sick'
                        )
                        ->count();


                    $permission =
                        $daily
                        ->filter(
                            fn ($status) =>
                                $status === 'permission'
                        )
                        ->count();


                    $absent =
                        $daily
                        ->filter(
                            fn ($status) =>
                                $status === 'absent'
                        )
                        ->count();

                @endphp


                <tr>

                    <td>
                        {{ $index + 1 }}
                    </td>


                    <td>
                        {{ $student->student->nis ?? '-' }}
                    </td>


                    <td class="text-left">
                        {{ $student->student->name ?? '-' }}
                    </td>


                    {{-- ==================================================
                         STATUS HARIAN
                         ================================================== --}}

                    @for (
                        $day = $monthStart->copy();
                        $day <= $monthEnd;
                        $day->addDay()
                    )

                        @php

                            $dateKey =
                                $day->format('Y-m-d');

                            $status =
                                $daily[$dateKey]
                                ?? null;

                        @endphp


                        <td>

                            @switch($status)

                                @case('present')
                                    H
                                    @break

                                @case('sick')
                                    S
                                    @break

                                @case('permission')
                                    I
                                    @break

                                @case('absent')
                                    A
                                    @break

                                @default
                                    -
                            @endswitch

                        </td>

                    @endfor


                    {{-- ==================================================
                         REKAP
                         ================================================== --}}

                    <td>
                        {{ $present }}
                    </td>

                    <td>
                        {{ $sick }}
                    </td>

                    <td>
                        {{ $permission }}
                    </td>

                    <td>
                        {{ $absent }}
                    </td>

                </tr>

            @endforeach

        </tbody>

    </table>


    <br>


    {{-- ==========================================================
         KETERANGAN
         ========================================================== --}}

    <table>

        <tr>

            <td class="info">
                H = Hadir
            </td>

            <td class="info">
                S = Sakit
            </td>

            <td class="info">
                I = Izin
            </td>

            <td class="info">
                A = Alpa
            </td>

        </tr>

    </table>


</body>
</html>
