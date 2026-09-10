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
            padding: 4px;
            text-align: center;
            vertical-align: middle;
        }

        th {
            font-weight: bold;
            background-color: #f2f2f2;
        }

        .text-left {
            text-align: left;
        }

        .title {
            font-size: 16px;
            font-weight: bold;
            text-align: center;
        }

        .subtitle {
            text-align: center;
        }
    </style>
</head>

<body>

    {{-- ==========================================================
         INFORMASI LAPORAN
         ========================================================== --}}

    <table>
        <tr>
            <td colspan="{{ $monthStart->daysInMonth + 7 }}" class="title">
                LAPORAN ABSENSI SISWA
            </td>
        </tr>

        <tr>
            <td colspan="{{ $monthStart->daysInMonth + 7 }}" class="subtitle">
                Bulan:
                {{ $monthStart->translatedFormat('F Y') }}
            </td>
        </tr>

        <tr>
            <td colspan="{{ $monthStart->daysInMonth + 7 }}" class="subtitle">
                Tahun Ajaran:
                {{ $academicYear?->name ?? '-' }}
            </td>
        </tr>

        <tr>
            <td colspan="{{ $monthStart->daysInMonth + 7 }}" class="subtitle">
                Unit:
                {{ $organization?->name ?? 'Semua Unit' }}
            </td>
        </tr>

        <tr>
            <td colspan="{{ $monthStart->daysInMonth + 7 }}" class="subtitle">
                Kelas:
                {{ $schoolClass?->name ?? 'Semua Kelas' }}
            </td>
        </tr>

        <tr>
            <td colspan="{{ $monthStart->daysInMonth + 7 }}" class="subtitle">
                Mata Pelajaran:
                {{ $subject?->name ?? 'Semua Mata Pelajaran' }}
            </td>
        </tr>
    </table>

    <br>


    {{-- ==========================================================
         TABEL ABSENSI
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

            @foreach ($students as $index => $student)

                @php

                    /*
                     * ==================================================
                     * STATUS HARIAN SISWA
                     * ==================================================
                     *
                     * dailyStatuses sudah dihitung di Controller.
                     *
                     * Key:
                     * studentAcademicYearId
                     *
                     * Value:
                     * tanggal => status
                     */

                    $daily =
                        $dailyStatuses[$student->id]
                        ?? collect();


                    /*
                     * ==================================================
                     * REKAP BULANAN
                     * ==================================================
                     *
                     * Dihitung berdasarkan HARI,
                     * bukan jumlah pertemuan.
                     */

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

                    {{-- No --}}
                    <td>
                        {{ $index + 1 }}
                    </td>


                    {{-- NIS --}}
                    <td>
                        {{ $student->student->nis ?? '-' }}
                    </td>


                    {{-- Nama --}}
                    <td class="text-left">
                        {{ $student->student->name ?? '-' }}
                    </td>


                    {{-- ==================================================
                         ABSENSI PER HARI
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
                                $daily[$dateKey] ?? null;

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

            <td class="text-left">
                H = Hadir
            </td>

            <td class="text-left">
                S = Sakit
            </td>

            <td class="text-left">
                I = Izin
            </td>

            <td class="text-left">
                A = Alpa
            </td>

        </tr>

    </table>

</body>
</html>
