<table>
    <tr>
        <th colspan="13">
            LAPORAN TAGIHAN
        </th>
    </tr>

    {{-- KETERANGAN FILTER --}}
    <tr>
        <th colspan="2">Tahun Ajaran</th>
        <td colspan="11">
            @if ($academicYearId === 'all' || empty($academicYearId))
                Semua Tahun Ajaran
            @else
                {{ $academicYears->firstWhere('id', $academicYearId)?->name ?? '-' }}
            @endif
        </td>
    </tr>

    <tr>
        <th colspan="2">Unit</th>
        <td colspan="11">
            @if ($organizationId === 'all' || empty($organizationId))
                Semua Unit
            @else
                {{ $organizations->firstWhere('id', $organizationId)?->name ?? '-' }}
            @endif
        </td>
    </tr>

    <tr>
        <th colspan="2">Jenis Tagihan</th>
        <td colspan="11">
            @if ($billTypeId === 'all' || empty($billTypeId))
                Semua Jenis Tagihan
            @else
                {{ $billTypes->firstWhere('id', $billTypeId)?->name ?? '-' }}
            @endif
        </td>
    </tr>

    <tr>
        <th colspan="2">Periode</th>
        <td colspan="11">
            {{ $period ?: 'Semua Periode' }}
        </td>
    </tr>

    <tr>
        <th colspan="2">Status</th>
        <td colspan="11">
            {{ $status === 'all' || empty($status) ? 'Semua Status' : ucfirst($status) }}
        </td>
    </tr>

    <tr>
        <td colspan="13"></td>
    </tr>

    {{-- RINGKASAN --}}
    <tr>
        <th>Total Tagihan</th>
        <td>{{ $totalBills }}</td>

        <th>Total Nominal</th>
        <td>{{ $totalBillAmount }}</td>

        <th>Sudah Dibayar</th>
        <td>{{ $totalPaidAmount }}</td>

        <th>Sisa Tagihan</th>
        <td>{{ $totalRemainingAmount }}</td>
    </tr>

    <tr>
        <td colspan="13"></td>
    </tr>

    <tr>
        <th colspan="13">
            REKAP TAGIHAN TIAP SISWA
        </th>
    </tr>

    <tr>
        <th>No</th>
        <th>Siswa</th>
        <th>NIS</th>
        <th>Unit</th>
        <th>Kelas</th>
        <th>Jml Tagihan</th>
        <th>Total Tagihan</th>
        <th>Sudah Dibayar</th>
        <th>Sisa Tagihan</th>
        <th>Lunas</th>
        <th>Sebagian</th>
        <th>Belum</th>
        <th>Batal</th>
    </tr>

    @foreach ($studentSummary as $index => $item)
        @php
            $student = $item['student'];
            $organization = $item['organization'];
            $schoolClass = $item['school_class'];
        @endphp

        <tr>
            <td>{{ $index + 1 }}</td>

            <td>{{ $student?->name ?? '-' }}</td>

            <td>{{ $student?->nis ?? '-' }}</td>

            <td>{{ $organization?->name ?? '-' }}</td>

            <td>{{ $schoolClass?->name ?? '-' }}</td>

            <td>{{ $item['bill_count'] }}</td>

            <td>{{ $item['total_bill_amount'] }}</td>

            <td>{{ $item['total_paid_amount'] }}</td>

            <td>{{ $item['total_remaining_amount'] }}</td>

            <td>{{ $item['paid_bill_count'] }}</td>

            <td>{{ $item['partial_bill_count'] }}</td>

            <td>{{ $item['unpaid_bill_count'] }}</td>

            <td>{{ $item['cancelled_bill_count'] }}</td>
        </tr>
    @endforeach

    <tr>
        <td colspan="13"></td>
    </tr>

    <tr>
        <th colspan="11">
            DETAIL TAGIHAN
        </th>
    </tr>

    <tr>
        <th>No</th>
        <th>Siswa</th>
        <th>NIS</th>
        <th>Unit</th>
        <th>Kelas</th>
        <th>Jenis Tagihan</th>
        <th>Periode</th>
        <th>Nominal</th>
        <th>Sudah Dibayar</th>
        <th>Sisa</th>
        <th>Status</th>
    </tr>

    @foreach ($bills as $index => $bill)
        @php
            $student = $bill->studentAcademicYear?->student;
            $organization = $bill->studentAcademicYear?->organization;
            $schoolClass = $bill->studentAcademicYear?->schoolClass;

            $amount = (float) $bill->amount;
            $paid = (float) ($paidAmounts[$bill->id] ?? 0);
            $paid = min($paid, $amount);
            $remaining = max($amount - $paid, 0);
        @endphp

        <tr>
            <td>{{ $index + 1 }}</td>

            <td>{{ $student?->name ?? '-' }}</td>

            <td>{{ $student?->nis ?? '-' }}</td>

            <td>{{ $organization?->name ?? '-' }}</td>

            <td>{{ $schoolClass?->name ?? '-' }}</td>

            <td>{{ $bill->billType?->name ?? '-' }}</td>

            <td>{{ $bill->period ?? '-' }}</td>

            <td>{{ $bill->amount }}</td>

            <td>{{ $paid }}</td>

            <td>{{ $remaining }}</td>

            <td>{{ ucfirst($bill->status) }}</td>
        </tr>
    @endforeach

</table>
