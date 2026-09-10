<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">

    <title>Laporan Tagihan</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;
            color: #222;
        }

        h1 {
            text-align: center;
            font-size: 16px;
            margin: 0 0 12px;
        }

        h2 {
            font-size: 11px;
            margin: 14px 0 6px;
            background: #eee;
            padding: 5px;
        }

        .filter {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .filter th,
        .filter td {
            border: 1px solid #999;
            padding: 4px 6px;
            text-align: left;
        }

        .filter th {
            width: 18%;
        }

        .summary {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }

        .summary th,
        .summary td {
            border: 1px solid #999;
            padding: 5px;
        }

        .summary th {
            text-align: center;
        }

        .summary td {
            text-align: right;
        }

        table.report {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }

        table.report th,
        table.report td {
            border: 1px solid #777;
            padding: 4px;
        }

        table.report th {
            text-align: center;
            background: #eee;
        }

        table.report td.number {
            text-align: right;
        }

        table.report td.center {
            text-align: center;
        }

        .page-break {
            page-break-before: always;
        }

        .small {
            font-size: 8px;
        }
    </style>
</head>

<body>

    <h1>LAPORAN TAGIHAN</h1>

    {{-- ========================================= --}}
    {{-- KETERANGAN FILTER --}}
    {{-- ========================================= --}}

    <table class="filter">

        <tr>
            <th>Tahun Ajaran</th>
            <td>
                @if ($academicYearId === 'all' || empty($academicYearId))
                    Semua Tahun Ajaran
                @else
                    {{ $academicYears->firstWhere('id', $academicYearId)?->name ?? '-' }}
                @endif
            </td>
        </tr>

        <tr>
            <th>Unit</th>
            <td>
                @if ($organizationId === 'all' || empty($organizationId))
                    Semua Unit
                @else
                    {{ $organizations->firstWhere('id', $organizationId)?->name ?? '-' }}
                @endif
            </td>
        </tr>

        <tr>
            <th>Jenis Tagihan</th>
            <td>
                @if ($billTypeId === 'all' || empty($billTypeId))
                    Semua Jenis Tagihan
                @else
                    {{ $billTypes->firstWhere('id', $billTypeId)?->name ?? '-' }}
                @endif
            </td>
        </tr>

        <tr>
            <th>Periode</th>
            <td>
                {{ $period ?: 'Semua Periode' }}
            </td>
        </tr>

        @php
            $statusLabel = match ($status) {
                'paid' => 'Lunas',
                'partial' => 'Sebagian',
                'unpaid' => 'Belum Lunas',
                'cancelled' => 'Batal',
                default => 'Semua Status',
            };
        @endphp

        <tr>
            <th>Status</th>
            <td>
                {{ $statusLabel }}
            </td>
        </tr>

    </table>


    {{-- ========================================= --}}
    {{-- RINGKASAN --}}
    {{-- ========================================= --}}

    <h2>RINGKASAN</h2>

    <table class="summary">

        <tr>
            <th>Total Tagihan</th>
            <th>Total Nominal</th>
            <th>Sudah Dibayar</th>
            <th>Sisa Tagihan</th>
        </tr>

        <tr>
            <td>{{ $totalBills }}</td>
            <td>Rp {{ number_format($totalBillAmount, 0, ',', '.') }}</td>
            <td>Rp {{ number_format($totalPaidAmount, 0, ',', '.') }}</td>
            <td>Rp {{ number_format($totalRemainingAmount, 0, ',', '.') }}</td>
        </tr>

    </table>


    {{-- ========================================= --}}
    {{-- REKAP TAGIHAN TIAP SISWA --}}
    {{-- ========================================= --}}

    <h2>REKAP TAGIHAN TIAP SISWA</h2>

    <table class="report">

        <thead>
            <tr>
                <th width="3%">No</th>
                <th width="15%">Siswa</th>
                <th width="8%">NIS</th>
                <th width="10%">Unit</th>
                <th width="10%">Kelas</th>
                <th width="6%">Jml</th>
                <th width="11%">Tagihan</th>
                <th width="11%">Dibayar</th>
                <th width="11%">Sisa</th>
                <th width="5%">Lunas</th>
                <th width="5%">Sebagian</th>
                <th width="5%">Belum</th>
                <th width="5%">Batal</th>
            </tr>
        </thead>

        <tbody>

            @foreach ($studentSummary as $index => $item)

                @php
                    $student = $item['student'];
                    $organization = $item['organization'];
                    $schoolClass = $item['school_class'];
                @endphp

                <tr>

                    <td class="center">
                        {{ $index + 1 }}
                    </td>

                    <td>
                        {{ $student?->name ?? '-' }}
                    </td>

                    <td>
                        {{ $student?->nis ?? '-' }}
                    </td>

                    <td>
                        {{ $organization?->name ?? '-' }}
                    </td>

                    <td>
                        {{ $schoolClass?->name ?? '-' }}
                    </td>

                    <td class="center">
                        {{ $item['bill_count'] }}
                    </td>

                    <td class="number">
                        {{ number_format($item['total_bill_amount'], 0, ',', '.') }}
                    </td>

                    <td class="number">
                        {{ number_format($item['total_paid_amount'], 0, ',', '.') }}
                    </td>

                    <td class="number">
                        {{ number_format($item['total_remaining_amount'], 0, ',', '.') }}
                    </td>

                    <td class="center">
                        {{ $item['paid_bill_count'] }}
                    </td>

                    <td class="center">
                        {{ $item['partial_bill_count'] }}
                    </td>

                    <td class="center">
                        {{ $item['unpaid_bill_count'] }}
                    </td>

                    <td class="center">
                        {{ $item['cancelled_bill_count'] }}
                    </td>

                </tr>

            @endforeach

        </tbody>

    </table>


    {{-- ========================================= --}}
    {{-- DETAIL TAGIHAN --}}
    {{-- ========================================= --}}

    <div class="page-break"></div>

    <h2>DETAIL TAGIHAN</h2>

    <table class="report">

        <thead>
            <tr>
                <th width="3%">No</th>
                <th width="15%">Siswa</th>
                <th width="8%">NIS</th>
                <th width="10%">Unit</th>
                <th width="10%">Kelas</th>
                <th width="12%">Jenis Tagihan</th>
                <th width="8%">Periode</th>
                <th width="10%">Nominal</th>
                <th width="10%">Dibayar</th>
                <th width="10%">Sisa</th>
                <th width="8%">Status</th>
            </tr>
        </thead>

        <tbody>

            @foreach ($bills as $index => $bill)

                @php
                    $student = $bill->studentAcademicYear?->student;
                    $organization = $bill->studentAcademicYear?->organization;
                    $schoolClass = $bill->studentAcademicYear?->schoolClass;

                    $amount = (float) $bill->amount;

                    $paid = (float) ($paidAmounts[$bill->id] ?? 0);

                    $paid = min($paid, $amount);

                    $remaining = max($amount - $paid, 0);

                    $detailStatus = match ($bill->status) {
                        'paid' => 'Lunas',
                        'partial' => 'Sebagian',
                        'unpaid' => 'Belum',
                        'cancelled' => 'Batal',
                        default => ucfirst($bill->status ?? '-'),
                    };
                @endphp

                <tr>

                    <td class="center">
                        {{ $index + 1 }}
                    </td>

                    <td>
                        {{ $student?->name ?? '-' }}
                    </td>

                    <td>
                        {{ $student?->nis ?? '-' }}
                    </td>

                    <td>
                        {{ $organization?->name ?? '-' }}
                    </td>

                    <td>
                        {{ $schoolClass?->name ?? '-' }}
                    </td>

                    <td>
                        {{ $bill->billType?->name ?? '-' }}
                    </td>

                    <td class="center">
                        {{ $bill->period ?? '-' }}
                    </td>

                    <td class="number">
                        {{ number_format($amount, 0, ',', '.') }}
                    </td>

                    <td class="number">
                        {{ number_format($paid, 0, ',', '.') }}
                    </td>

                    <td class="number">
                        {{ number_format($remaining, 0, ',', '.') }}
                    </td>

                    <td class="center">
                        {{ $detailStatus }}
                    </td>

                </tr>

            @endforeach

        </tbody>

    </table>

</body>
</html>
