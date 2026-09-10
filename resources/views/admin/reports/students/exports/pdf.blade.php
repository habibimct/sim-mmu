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

    </style>

</head>

<body>

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

                        {{ $student?->is_active
                            ? 'Aktif'
                            : 'Tidak Aktif'
                        }}

                    </td>

                </tr>

            @endforeach

        </tbody>

    </table>

</body>

</html>
