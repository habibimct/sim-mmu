<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <title>
        Laporan Guru
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
        LAPORAN GURU
    </h2>


    <div class="subtitle">
        Rekapitulasi Data Guru
    </div>


    <table>

        <thead>

            <tr>

                <th width="35">
                    No
                </th>

                <th>
                    NIK
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
                    Status
                </th>

            </tr>

        </thead>


        <tbody>

            @foreach ($teachers as $index => $teacher)

                <tr>

                    <td class="center">
                        {{ $index + 1 }}
                    </td>


                    <td>
                        {{ $teacher->nik ?? '-' }}
                    </td>


                    <td>
                        {{ $teacher->name }}
                    </td>


                    <td class="center">

                        @if ($teacher->gender === 'male')

                            L

                        @elseif ($teacher->gender === 'female')

                            P

                        @else

                            -

                        @endif

                    </td>


                    <td>

                        {{
                            $teacher->organizations
                                ->pluck('name')
                                ->implode(', ')
                        }}

                    </td>


                    <td class="center">

                        {{
                            $teacher->is_active
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
