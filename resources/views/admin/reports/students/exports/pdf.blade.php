<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <title>
        Laporan Siswa
    </title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #222;
        }

        h2 {
            text-align: center;
            margin-bottom: 5px;
        }

        .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #999;
            padding: 6px;
        }

        th {
            background: #eeeeee;
            text-align: center;
        }

        .center {
            text-align: center;
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


    {{-- ==========================================================
     JUDUL LAPORAN
     ========================================================== --}}

    <h2>
        LAPORAN SISWA
    </h2>

    <div class="subtitle">
        Rekapitulasi Data Siswa
    </div>

    <table>

        <thead>

            <tr>

                <th width="35">
                    No
                </th>

                <th>
                    NIS
                </th>

                <th>
                    Nama
                </th>

                <th width="40">
                    L/P
                </th>

                <th>
                    Unit
                </th>

                <th>
                    Kelas
                </th>

                <th>
                    Status
                </th>

            </tr>

        </thead>

        <tbody>

            @foreach ($studentAcademicYears as $index => $studentAcademicYear)
                @php
                    $student = $studentAcademicYear->student;
                @endphp

                <tr>

                    <td class="center">
                        {{ $index + 1 }}
                    </td>

                    <td>
                        {{ $student?->nis ?? '-' }}
                    </td>

                    <td>
                        {{ $student?->name ?? '-' }}
                    </td>

                    <td class="center">
                        {{ $student?->gender ?? '-' }}
                    </td>

                    <td>
                        {{ $studentAcademicYear->organization?->name ?? '-' }}
                    </td>

                    <td>
                        {{ $studentAcademicYear->schoolClass?->name ?? '-' }}
                    </td>

                    <td class="center">

                        {{ $student?->is_active ? 'Aktif' : 'Tidak Aktif' }}

                    </td>

                </tr>
            @endforeach

        </tbody>

    </table>

</body>

</html>
