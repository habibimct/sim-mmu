<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <title>Laporan Keuangan</title>

    <style>
        @page {
            size: A4 landscape;
            margin: 18mm 15mm 15mm 15mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;
            color: #222;
            margin: 0;
        }

        .kop {
            width: 100%;
            border-bottom: 2px solid #222;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }

        .kop-table {
            width: 100%;
            border-collapse: collapse;
        }

        .kop-logo {
            width: 25%;
            text-align: left;
            vertical-align: middle;
        }

        .kop-logo img {
            width: 60px;
            height: 60px;
            object-fit: contain;
        }

        .kop-identitas {
            width: 50%;
            vertical-align: middle;
            text-align: center;
            padding-left: 8px;
        }

        .kop-identitas .nama {
            font-size: 15px;
            font-weight: bold;
        }

        .kop-identitas .subnama {
            margin-top: 3px;
            font-size: 10px;
        }

        .kop-contact {
            width: 25%;
            text-align: right;
            vertical-align: middle;
            line-height: 1.5;
            font-size: 8px;
        }

        .judul {
            text-align: center;
            margin-bottom: 3px;
            font-size: 14px;
            font-weight: bold;
        }

        .periode {
            text-align: center;
            margin-bottom: 12px;
            font-size: 9px;
        }

        .filter-info {
            width: 100%;
            margin-bottom: 12px;
        }

        .filter-info table {
            width: 100%;
            border-collapse: collapse;
        }

        .filter-info td {
            padding: 3px 5px;
            border: 1px solid #ddd;
        }

        .filter-label {
            width: 12%;
            font-weight: bold;
            background: #f5f5f5;
        }

        .summary {
            width: 100%;
            margin-bottom: 15px;
        }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }

        .summary-table td {
            width: 25%;
            border: 1px solid #ccc;
            padding: 8px;
            vertical-align: top;
        }

        .summary-label {
            font-size: 8px;
            color: #666;
            margin-bottom: 4px;
        }

        .summary-value {
            font-size: 12px;
            font-weight: bold;
        }

        .section-title {
            font-size: 10px;
            font-weight: bold;
            margin: 10px 0 6px 0;
        }

        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }

        table.data th {
            background: #f1f1f1;
            border: 1px solid #bbb;
            padding: 5px;
            text-align: left;
            font-weight: bold;
        }

        table.data td {
            border: 1px solid #ccc;
            padding: 5px;
            vertical-align: top;
        }

        table.data .text-right {
            text-align: right;
        }

        table.data .text-center {
            text-align: center;
        }

        .total-row td {
            background: #f5f5f5;
            font-weight: bold;
        }

        .page-break {
            page-break-before: always;
        }

        .footer {
            margin-top: 15px;
            text-align: right;
            font-size: 8px;
            color: #666;
        }
    </style>
</head>

<body>

    {{-- =========================================================
        KOP LAPORAN
    ========================================================== --}}

    <div class="kop">

        <table class="kop-table">

            <tr>

                <td class="kop-logo">

                    @if ($induk?->logo_path)

                        @php
                            $logoPath = public_path('storage/' . $induk->logo_path);
                        @endphp

                        @if (file_exists($logoPath))
                            <img src="{{ $logoPath }}" alt="Logo">
                        @endif

                    @endif

                </td>

                <td class="kop-identitas">

                    <div class="nama">
                        {{ $induk->name }}
                    </div>

                    <div class="subnama">
                        Laporan Keuangan
                    </div>

                </td>

                <td class="kop-contact">

                    @if ($induk->address)
                        {{ $induk->address }}<br>
                    @endif

                    @if ($induk->phone)
                        Telp. {{ $induk->phone }}<br>
                    @endif

                    @if ($induk->email)
                        {{ $induk->email }}<br>
                    @endif

                    @if ($induk->website)
                        {{ $induk->website }}
                    @endif

                </td>

            </tr>

        </table>

    </div>


    {{-- =========================================================
        JUDUL
    ========================================================== --}}

    <div class="judul">
        LAPORAN KEUANGAN
    </div>

    <div class="periode">
        Periode
        {{ \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') }}
        s.d.
        {{ \Carbon\Carbon::parse($dateTo)->format('d/m/Y') }}
    </div>


    {{-- =========================================================
        FILTER
    ========================================================== --}}

    <div class="filter-info">

        <table>

            <tr>
                <td class="filter-label">
                    Organisasi
                </td>

                <td>
                    @if ($organizationId === 'all')
                        Semua Unit
                    @else
                        @php
                            $selectedOrganization = $organizations
                                ->firstWhere('id', $organizationId);
                        @endphp

                        {{ $selectedOrganization?->type === 'induk'
                            ? 'PMUB'
                            : ($selectedOrganization?->name ?? '-') }}
                    @endif
                </td>

                <td class="filter-label">
                    Kategori
                </td>

                <td>
                    @switch($reportType)

                        @case('income')
                            Pemasukan
                            @break

                        @case('expense')
                            Pengeluaran
                            @break

                        @case('deposit')
                            Setoran
                            @break

                        @default
                            Semua
                    @endswitch
                </td>

            </tr>

        </table>

    </div>


    {{-- =========================================================
        RINGKASAN
    ========================================================== --}}

    <div class="section-title">
        Ringkasan
    </div>

    <div class="summary">

        <table class="summary-table">

            <tr>

                <td>
                    <div class="summary-label">
                        Total Pemasukan
                    </div>

                    <div class="summary-value">
                        Rp {{ number_format($totalIncome, 0, ',', '.') }}
                    </div>
                </td>

                <td>
                    <div class="summary-label">
                        Total Pengeluaran
                    </div>

                    <div class="summary-value">
                        Rp {{ number_format($totalExpense, 0, ',', '.') }}
                    </div>
                </td>

                <td>
                    <div class="summary-label">
                        Total Setoran
                    </div>

                    <div class="summary-value">
                        Rp {{ number_format($totalDeposit, 0, ',', '.') }}
                    </div>
                </td>

                <td>
                    <div class="summary-label">
                        Saldo
                    </div>

                    <div class="summary-value">
                        Rp {{ number_format($netBalance, 0, ',', '.') }}
                    </div>
                </td>

            </tr>

        </table>

    </div>


    {{-- =========================================================
        REKAP PER UNIT
    ========================================================== --}}

    <div class="section-title">
        Rekap Keuangan per Unit
    </div>

    <table class="data">

        <thead>

            <tr>
                <th>No.</th>
                <th>Organisasi</th>
                <th class="text-right">Pemasukan</th>
                <th class="text-right">Pengeluaran</th>
                <th class="text-right">Setoran</th>
                <th class="text-right">Saldo</th>
            </tr>

        </thead>

        <tbody>

            @forelse ($organizationSummary as $row)

                <tr>

                    <td class="text-center">
                        {{ $loop->iteration }}
                    </td>

                    <td>
                        {{ $row->organization?->type === 'induk'
                            ? 'PMUB'
                            : ($row->organization?->name ?? '-') }}
                    </td>

                    <td class="text-right">
                        Rp {{ number_format($row->total_income, 0, ',', '.') }}
                    </td>

                    <td class="text-right">
                        Rp {{ number_format($row->total_expense, 0, ',', '.') }}
                    </td>

                    <td class="text-right">
                        Rp {{ number_format($row->total_deposit, 0, ',', '.') }}
                    </td>

                    <td class="text-right">
                        Rp {{ number_format($row->net_balance, 0, ',', '.') }}
                    </td>

                </tr>

            @empty

                <tr>
                    <td colspan="6" class="text-center">
                        Tidak ada data.
                    </td>
                </tr>

            @endforelse

        </tbody>

        @if ($organizationSummary->isNotEmpty())

            <tfoot>

                <tr class="total-row">

                    <td colspan="2">
                        TOTAL
                    </td>

                    <td class="text-right">
                        Rp {{ number_format($organizationSummary->sum('total_income'), 0, ',', '.') }}
                    </td>

                    <td class="text-right">
                        Rp {{ number_format($organizationSummary->sum('total_expense'), 0, ',', '.') }}
                    </td>

                    <td class="text-right">
                        Rp {{ number_format($organizationSummary->sum('total_deposit'), 0, ',', '.') }}
                    </td>

                    <td class="text-right">
                        Rp {{ number_format($organizationSummary->sum('net_balance'), 0, ',', '.') }}
                    </td>

                </tr>

            </tfoot>

        @endif

    </table>


    {{-- =========================================================
        DETAIL TRANSAKSI
    ========================================================== --}}

    <div class="section-title">
        Detail Transaksi
    </div>

    <table class="data">

        <thead>

            <tr>
                <th>No.</th>
                <th>Tanggal</th>
                <th>Organisasi</th>
                <th>Jenis</th>
                <th>Kategori</th>
                <th class="text-right">Jumlah</th>
                <th>Metode</th>
                <th>Keterangan</th>
            </tr>

        </thead>

        <tbody>

            @forelse ($transactions as $transaction)

                @php
                    $isDeposit =
                        $transaction->source_type === 'deposit';
                @endphp

                <tr>

                    <td class="text-center">
                        {{ $loop->iteration }}
                    </td>

                    <td>
                        {{ $transaction->transaction_date?->format('d/m/Y') ?? '-' }}
                    </td>

                    <td>
                        {{ $transaction->organization?->type === 'induk'
                            ? 'PMUB'
                            : ($transaction->organization?->name ?? '-') }}
                    </td>

                    <td>
                        @if ($isDeposit)
                            Setoran
                        @elseif ($transaction->type === 'income')
                            Pemasukan
                        @elseif ($transaction->type === 'expense')
                            Pengeluaran
                        @else
                            -
                        @endif
                    </td>

                    <td>
                        {{ $isDeposit
                            ? 'Setoran Unit'
                            : ($transaction->category ?? '-') }}
                    </td>

                    <td class="text-right">
                        Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                    </td>

                    <td>
                        {{ $transaction->payment_method ?? '-' }}
                    </td>

                    <td>
                        {{ $transaction->description ?? '-' }}
                    </td>

                </tr>

            @empty

                <tr>
                    <td colspan="8" class="text-center">
                        Tidak ada transaksi.
                    </td>
                </tr>

            @endforelse

        </tbody>

    </table>


    {{-- =========================================================
        FOOTER
    ========================================================== --}}

    <div class="footer">
        Dicetak pada
        {{ now()->format('d/m/Y H:i') }}
    </div>

</body>

</html>
