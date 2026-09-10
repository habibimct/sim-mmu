<table>
    <thead>
        <tr>
            <th colspan="7">
                LAPORAN SISWA
            </th>
        </tr>

        <tr>
            <th>No</th>
            <th>NIS</th>
            <th>Nama</th>
            <th>L/P</th>
            <th>Unit</th>
            <th>Kelas</th>
            <th>Status</th>
        </tr>
    </thead>

    <tbody>

        @foreach ($studentAcademicYears as $index => $studentAcademicYear)

            @php
                $student = $studentAcademicYear->student;
            @endphp

            <tr>

                <td>
                    {{ $index + 1 }}
                </td>

                <td>
                    {{ $student?->nis ?? '-' }}
                </td>

                <td>
                    {{ $student?->name ?? '-' }}
                </td>

                <td>
                    {{ $student?->gender ?? '-' }}
                </td>

                <td>
                    {{ $studentAcademicYear->organization?->name ?? '-' }}
                </td>

                <td>
                    {{ $studentAcademicYear->schoolClass?->name ?? '-' }}
                </td>

                <td>
                    {{ $student?->is_active ? 'Aktif' : 'Tidak Aktif' }}
                </td>

            </tr>

        @endforeach

    </tbody>
</table>
