<table>

    <thead>

        <tr>

            <th colspan="6">
                LAPORAN GURU
            </th>

        </tr>


        <tr>

            <th>No</th>
            <th>NIK</th>
            <th>Nama</th>
            <th>L/P</th>
            <th>Unit</th>
            <th>Status</th>

        </tr>

    </thead>


    <tbody>

        @foreach ($teachers as $index => $teacher)
            <tr>

                <td>
                    {{ $index + 1 }}
                </td>


                <td>
                    {{ $teacher->nik ?? '-' }}
                </td>


                <td>
                    {{ $teacher->name }}
                </td>


                <td>

                    @if ($teacher->gender === 'male')
                        L
                    @elseif ($teacher->gender === 'female')
                        P
                    @else
                        -
                    @endif

                </td>


                <td>

                    {{ $teacher->organizations->pluck('name')->implode(', ') }}

                </td>


                <td>

                    {{ $teacher->is_active ? 'Aktif' : 'Tidak Aktif' }}

                </td>

            </tr>
        @endforeach

    </tbody>

</table>
