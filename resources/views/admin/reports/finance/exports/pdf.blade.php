<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <title>Laporan Keuangan</title>

    <style>
        /*
        |--------------------------------------------------------------------------
        | PAGE
        |--------------------------------------------------------------------------
        */

        @page {
            margin: 18px 20px 20px 20px;
        }

        /*
        |--------------------------------------------------------------------------
        | GLOBAL
        |--------------------------------------------------------------------------
        */

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;

            font-family: "DejaVu Sans", sans-serif;

            font-size: 8.5px;
            line-height: 1.45;

            color: #1f2937;

            background: #ffffff;
        }

        table {
            border-collapse: collapse;
        }

        /*
        |--------------------------------------------------------------------------
        | HEADER
        |--------------------------------------------------------------------------
        */

        .report-header {
            width: 100%;
            margin-bottom: 14px;
            padding-bottom: 12px;

            border-bottom: 2px solid #1f2937;
        }

        .report-header-table {
            width: 100%;
        }

        .report-header-left {
            width: 70%;
            vertical-align: top;
        }

        .report-header-right {
            width: 30%;
            text-align: right;
            vertical-align: top;
        }

        .report-label {
            font-size: 7px;
            font-weight: bold;

            letter-spacing: 1.2px;

            color: #6b7280;

            text-transform: uppercase;
        }

        .report-title {
            margin-top: 3px;

            font-size: 21px;
            line-height: 1.2;

            font-weight: bold;

            letter-spacing: -0.3px;

            color: #111827;
        }

        .report-description {
            margin-top: 4px;

            font-size: 8px;

            color: #6b7280;
        }

        .report-date-box {
            display: inline-block;

            padding: 7px 10px;

            border: 1px solid #e5e7eb;

            background: #f8fafc;
        }

        .report-date-label {
            font-size: 6.5px;

            color: #9ca3af;

            text-transform: uppercase;

            letter-spacing: 0.7px;
        }

        .report-date-value {
            margin-top: 2px;

            font-size: 8px;
            font-weight: bold;

            color: #374151;
        }

        /*
        |--------------------------------------------------------------------------
        | FILTER / METADATA
        |--------------------------------------------------------------------------
        */

        .filter-box {
            width: 100%;

            margin-bottom: 14px;

            padding: 8px 10px;

            border: 1px solid #e5e7eb;

            background: #fafafa;
        }

        .filter-table {
            width: 100%;
        }

        .filter-item {
            width: 25%;

            vertical-align: top;

            padding-right: 10px;
        }

        .filter-item:last-child {
            padding-right: 0;
        }

        .filter-label {
            font-size: 6.5px;

            color: #9ca3af;

            text-transform: uppercase;

            letter-spacing: 0.6px;
        }

        .filter-value {
            margin-top: 2px;

            font-size: 8px;

            font-weight: bold;

            color: #374151;
        }

        /*
        |--------------------------------------------------------------------------
        | SUMMARY
        |--------------------------------------------------------------------------
        */

        .summary-wrapper {
            width: 100%;

            margin-bottom: 15px;
        }

        .summary-table {
            width: 100%;
        }

        .summary-card {
            width: 33.333%;

            padding: 10px 12px;

            vertical-align: top;

            border: 1px solid #e5e7eb;

            background: #ffffff;
        }

        .summary-card-spacer {
            width: 10px;
        }

        .summary-label {
            font-size: 7px;

            color: #6b7280;

            text-transform: uppercase;

            letter-spacing: 0.5px;
        }

        .summary-value {
            margin-top: 5px;

            font-size: 14px;

            line-height: 1.2;

            font-weight: bold;
        }

        .summary-income {
            color: #15803d;
        }

        .summary-expense {
            color: #dc2626;
        }

        .summary-balance {
            color: #1d4ed8;
        }

        .summary-caption {
            margin-top: 4px;

            font-size: 6.5px;

            color: #9ca3af;
        }

        /*
        |--------------------------------------------------------------------------
        | SECTION
        |--------------------------------------------------------------------------
        */

        .section {
            margin-top: 14px;
            margin-bottom: 7px;
        }

        .section-table {
            width: 100%;
        }

        .section-title {
            font-size: 10px;

            font-weight: bold;

            color: #111827;
        }

        .section-line {
            border-bottom: 1px solid #e5e7eb;
        }

        /*
        |--------------------------------------------------------------------------
        | TABLE
        |--------------------------------------------------------------------------
        */

        .report-table {
            width: 100%;

            margin-bottom: 12px;
        }

        .report-table thead {
            display: table-header-group;
        }

        .report-table th {
            padding: 6px 6px;

            border-top: 1px solid #d1d5db;
            border-bottom: 1px solid #d1d5db;

            background: #f8fafc;

            color: #374151;

            font-size: 7px;

            font-weight: bold;

            text-transform: uppercase;

            letter-spacing: 0.2px;

            text-align: left;
        }

        .report-table td {
            padding: 5px 6px;

            border-bottom: 1px solid #eef0f2;

            font-size: 7.5px;

            color: #374151;

            vertical-align: top;
        }

        .report-table tbody tr:nth-child(even) {
            background: #fcfcfd;
        }

        .report-table tfoot td {
            padding: 6px;

            border-top: 1px solid #d1d5db;

            background: #f8fafc;

            font-weight: bold;
        }

        /*
        |--------------------------------------------------------------------------
        | BADGES
        |--------------------------------------------------------------------------
        */

        .badge-income {
            padding: 3px 6px;

            background: #ecfdf3;

            color: #15803d;

            font-size: 6.5px;

            font-weight: bold;
        }

        .badge-expense {
            padding: 3px 6px;

            background: #fef2f2;

            color: #dc2626;

            font-size: 6.5px;

            font-weight: bold;
        }

        /*
        |--------------------------------------------------------------------------
        | ALIGNMENT
        |--------------------------------------------------------------------------
        */

        .text-left {
            text-align: left;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .nowrap {
            white-space: nowrap;
        }

        /*
        |--------------------------------------------------------------------------
        | EMPTY
        |--------------------------------------------------------------------------
        */

        .empty-row {
            padding: 14px !important;

            text-align: center;

            color: #9ca3af !important;
        }

        /* =========================================================
   HALAMAN GRAFIK INDIVIDUAL
   ========================================================= */

        .chart-page {
            page-break-before: always;
            page-break-after: always;

            padding-top: 5px;

            width: 100%;
        }


        /* =========================================================
   HEADER GRAFIK
   ========================================================= */

        .charts-header {
            width: 100%;

            margin-bottom: 18px;

            padding-bottom: 11px;

            border-bottom: 2px solid #1f2937;
        }

        .charts-header-table {
            width: 100%;
        }

        .charts-header-left {
            width: 72%;

            vertical-align: top;
        }

        .charts-header-right {
            width: 28%;

            vertical-align: top;

            text-align: right;
        }

        .charts-kicker {
            font-size: 7px;

            font-weight: bold;

            color: #6b7280;

            letter-spacing: 1.2px;
        }

        .charts-title {
            margin-top: 3px;

            font-size: 20px;

            line-height: 1.2;

            font-weight: bold;

            color: #111827;
        }

        .charts-description {
            margin-top: 5px;

            font-size: 8px;

            color: #6b7280;
        }


        /* =========================================================
   PERIODE
   ========================================================= */

        .charts-period {
            display: inline-block;

            min-width: 170px;

            padding: 8px 10px;

            border: 1px solid #e5e7eb;

            background: #f8fafc;

            text-align: left;
        }

        .charts-period-label {
            font-size: 6px;

            color: #9ca3af;

            letter-spacing: 0.7px;

            text-transform: uppercase;
        }

        .charts-period-value {
            margin-top: 2px;

            font-size: 7.5px;

            font-weight: bold;

            color: #374151;
        }


        /* =========================================================
   CARD GRAFIK BESAR
   ========================================================= */

        .chart-card-large {
            width: 100%;

            padding: 14px 15px 12px 15px;

            border: 1px solid #e5e7eb;

            background: #ffffff;

            page-break-inside: avoid;
        }

        .chart-card-title {
            font-size: 11px;

            font-weight: bold;

            color: #111827;
        }

        .chart-card-description {
            margin-top: 3px;

            font-size: 7px;

            color: #9ca3af;
        }


        /* =========================================================
   GAMBAR GRAFIK
   ========================================================= */

        .chart-image-large {
            width: 100%;

            margin-top: 10px;

            text-align: center;
        }

        .chart-image-large img {
            display: block;

            width: 100%;

            height: auto;
        }


        /* =========================================================
   RINGKASAN MINI
   ========================================================= */

        .chart-summary {
            width: 100%;

            margin-top: 12px;
        }

        .chart-summary table {
            width: 100%;

            border-collapse: collapse;
        }

        .chart-summary td {
            width: 33.333%;

            padding: 9px 12px;

            border: 1px solid #e5e7eb;

            vertical-align: top;
        }

        .mini-label {
            font-size: 6px;

            color: #9ca3af;

            letter-spacing: 0.6px;
        }

        .mini-value {
            margin-top: 4px;

            font-size: 10px;

            font-weight: bold;
        }

        .income-text {
            color: #15803d;
        }

        .expense-text {
            color: #dc2626;
        }

        .balance-text {
            color: #1d4ed8;
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

        .header {
            width: 100%;
            text-align: center;
            font-size: 11px;
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


        /* =========================================================
   FOOTER
   ========================================================= */

        .charts-footer {
            margin-top: 15px;

            padding-top: 7px;

            border-top: 1px solid #e5e7eb;

            text-align: center;

            font-size: 6.5px;

            color: #9ca3af;
        }
    </style>

</head>


<body>

    @php
        /*
    |--------------------------------------------------------------------------
    | Kompatibilitas data
    |--------------------------------------------------------------------------
    |
    | buildReportData() menggunakan selectedOrganization,
    | sedangkan versi sebelumnya menggunakan organization.
    |
    */

        $reportOrganization = $selectedOrganization ?? ($organization ?? null);
    @endphp


    {{-- ==========================================================
     KOP LAPORAN
     ========================================================== --}}

    <table class="kop">

        <tr>

            {{-- LOGO --}}
            <td class="logo-cell">

                @if ($reportOrganization?->logo_path)
                    <img src="{{ public_path('storage/' . $reportOrganization->logo_path) }}" class="logo">
                @elseif ($induk?->logo_path)
                    <img src="{{ public_path('storage/' . $induk->logo_path) }}" class="logo">
                @endif

            </td>


            {{-- IDENTITAS ORGANISASI --}}
            <td class="kop-text">

                <div class="organization-name">
                    {{ $reportOrganization?->name ?? ($induk?->name ?? 'Perkumpulan Mamba\'ul Ulum Bedanten') }}
                </div>

                @if ($reportOrganization?->address ?? $induk?->address)
                    <div>
                        {{ $reportOrganization?->address ?? $induk?->address }}
                    </div>
                @endif

            </td>


            {{-- KONTAK --}}
            <td class="kop-text-right">

                @if ($reportOrganization?->phone ?? $induk?->phone)
                    <div>
                        Telp. {{ $reportOrganization?->phone ?? $induk?->phone }}
                    </div>
                @endif

                @if ($reportOrganization?->email ?? $induk?->email)
                    <div>
                        Email: {{ $reportOrganization?->email ?? $induk?->email }}
                    </div>
                @endif

                @if ($reportOrganization?->website ?? $induk?->website)
                    <div>
                        {{ $reportOrganization?->website ?? $induk?->website }}
                    </div>
                @endif

            </td>

        </tr>

    </table>

    <div class="kop-line"></div>


    {{-- ==========================================================
     JUDUL LAPORAN
     ========================================================== --}}

    <h1 class="header">
        LAPORAN KEUANGAN
    </h1>

    <div class="subtitle">
        Periode:
        {{ \Carbon\Carbon::parse($dateFrom)->translatedFormat('d F Y') }}
        &ndash;
        {{ \Carbon\Carbon::parse($dateTo)->translatedFormat('d F Y') }}
    </div>


    {{-- ========================================================= --}}
    {{-- FILTER --}}
    {{-- ========================================================= --}}

    <div class="filter-box">

        <table class="filter-table">

            <tr>

                <td class="filter-item">

                    <div class="filter-label">
                        Unit
                    </div>

                    <div class="filter-value">
                        {{ $reportOrganization?->name ?? 'Semua Unit' }}
                    </div>

                </td>


                <td class="filter-item">

                    <div class="filter-label">
                        Jenis Transaksi
                    </div>

                    <div class="filter-value">

                        @if ($type === 'income')
                            Pemasukan
                        @elseif ($type === 'expense')
                            Pengeluaran
                        @else
                            Semua Jenis
                        @endif

                    </div>

                </td>


                <td class="filter-item">

                    <div class="filter-label">
                        Kategori
                    </div>

                    <div class="filter-value">

                        {{ $category === 'all' ? 'Semua Kategori' : $category }}

                    </div>

                </td>


                <td class="filter-item">

                    <div class="filter-label">
                        Status Data
                    </div>

                    <div class="filter-value">
                        Transaksi Terkonfirmasi
                    </div>

                </td>

            </tr>

        </table>

    </div>


    {{-- ========================================================= --}}
    {{-- RINGKASAN --}}
    {{-- ========================================================= --}}

    <div class="summary-wrapper">

        <table class="summary-table">

            <tr>

                <td class="summary-card">

                    <div class="summary-label">
                        Total Pemasukan
                    </div>

                    <div class="summary-value summary-income">

                        Rp
                        {{ number_format($totalIncome, 0, ',', '.') }}

                    </div>

                    <div class="summary-caption">
                        Total transaksi pemasukan
                    </div>

                </td>


                <td class="summary-card-spacer">
                </td>


                <td class="summary-card">

                    <div class="summary-label">
                        Total Pengeluaran
                    </div>

                    <div class="summary-value summary-expense">

                        Rp
                        {{ number_format($totalExpense, 0, ',', '.') }}

                    </div>

                    <div class="summary-caption">
                        Total transaksi pengeluaran
                    </div>

                </td>


                <td class="summary-card-spacer">
                </td>


                <td class="summary-card">

                    <div class="summary-label">
                        Saldo Bersih
                    </div>

                    <div class="summary-value summary-balance">

                        Rp
                        {{ number_format($netBalance, 0, ',', '.') }}

                    </div>

                    <div class="summary-caption">
                        Pemasukan dikurangi pengeluaran
                    </div>

                </td>

            </tr>

        </table>

    </div>


    {{-- ========================================================= --}}
    {{-- REKAP PER UNIT --}}
    {{-- ========================================================= --}}

    <div class="section">

        <table class="section-table">

            <tr>

                <td class="section-title">
                    Rekap Keuangan per Unit
                </td>

                <td class="section-line">
                </td>

            </tr>

        </table>

    </div>


    <table class="report-table">

        <thead>

            <tr>

                <th width="5%" class="text-center">
                    No
                </th>

                <th>
                    Unit
                </th>

                <th width="22%" class="text-right">
                    Pemasukan
                </th>

                <th width="22%" class="text-right">
                    Pengeluaran
                </th>

                <th width="22%" class="text-right">
                    Saldo
                </th>

            </tr>

        </thead>


        <tbody>

            @forelse ($organizationSummary
            as $index => $row)
                <tr>

                    <td class="text-center">
                        {{ $index + 1 }}
                    </td>

                    <td>
                        {{ $row->organization?->name ?? '-' }}
                    </td>

                    <td class="text-right nowrap">
                        Rp
                        {{ number_format($row->total_income, 0, ',', '.') }}
                    </td>

                    <td class="text-right nowrap">
                        Rp
                        {{ number_format($row->total_expense, 0, ',', '.') }}
                    </td>

                    <td class="text-right nowrap">
                        Rp
                        {{ number_format($row->net_balance, 0, ',', '.') }}
                    </td>

                </tr>

            @empty

                <tr>

                    <td colspan="5" class="empty-row">
                        Tidak ada data keuangan pada periode ini.
                    </td>

                </tr>
            @endforelse

        </tbody>

    </table>


    {{-- ========================================================= --}}
    {{-- DETAIL TRANSAKSI --}}
    {{-- ========================================================= --}}

    <div class="section">

        <table class="section-table">

            <tr>

                <td class="section-title">
                    Detail Transaksi
                </td>

                <td class="section-line">
                </td>

            </tr>

        </table>

    </div>


    <table class="report-table">

        <thead>

            <tr>

                <th width="4%" class="text-center">
                    No
                </th>

                <th width="9%">
                    Tanggal
                </th>

                <th width="14%">
                    Unit
                </th>

                <th width="9%">
                    Jenis
                </th>

                <th width="13%">
                    Kategori
                </th>

                <th>
                    Keterangan
                </th>

                <th width="11%">
                    Metode
                </th>

                <th width="14%" class="text-right">
                    Nominal
                </th>

            </tr>

        </thead>


        <tbody>

            @forelse ($transactions
            as $index => $transaction)
                <tr>

                    <td class="text-center">
                        {{ $index + 1 }}
                    </td>


                    <td class="nowrap">

                        {{ $transaction->transaction_date?->format('d/m/Y') }}

                    </td>


                    <td>

                        {{ $transaction->organization?->name ?? '-' }}

                    </td>


                    <td>

                        @if ($transaction->type === 'income')
                            <span class="badge-income">
                                Pemasukan
                            </span>
                        @else
                            <span class="badge-expense">
                                Pengeluaran
                            </span>
                        @endif

                    </td>


                    <td>

                        {{ $transaction->category }}

                    </td>


                    <td>

                        {{ $transaction->description ?: '-' }}

                    </td>


                    <td>

                        {{ match ($transaction->payment_method) {
                            'cash' => 'Tunai',

                            'bank_transfer' => 'Transfer Bank',

                            'online' => 'Online',

                            default => $transaction->payment_method,
                        } }}

                    </td>


                    <td class="text-right nowrap">

                        Rp
                        {{ number_format($transaction->amount, 0, ',', '.') }}

                    </td>

                </tr>

            @empty

                <tr>

                    <td colspan="8" class="empty-row">
                        Tidak ada transaksi pada periode ini.
                    </td>

                </tr>
            @endforelse

        </tbody>

    </table>


    {{-- ========================================================= --}}
    {{-- HALAMAN GRAFIK 1 --}}
    {{-- ========================================================= --}}

    <div class="chart-page">

        <div class="charts-header">

            <table class="charts-header-table">

                <tr>

                    <td class="charts-header-left">

                        <div class="charts-kicker">
                            ANALISIS KEUANGAN
                        </div>

                        <div class="charts-title">
                            Tren Pemasukan &amp; Pengeluaran
                        </div>

                        <div class="charts-description">
                            Perkembangan transaksi keuangan
                            berdasarkan periode laporan.
                        </div>

                    </td>


                    <td class="charts-header-right">

                        <div class="charts-period">

                            <div class="charts-period-label">
                                PERIODE
                            </div>

                            <div class="charts-period-value">

                                {{ $startDate->translatedFormat('d F Y') }}

                                &ndash;

                                {{ $endDate->translatedFormat('d F Y') }}

                            </div>

                            <div class="charts-period-label" style="margin-top: 4px;">
                                UNIT
                            </div>

                            <div class="charts-period-value">

                                {{ $reportOrganization?->name ?? 'Semua Unit' }}

                            </div>

                        </div>

                    </td>

                </tr>

            </table>

        </div>


        <div class="chart-card-large">

            <div class="chart-card-title">
                Tren Pemasukan &amp; Pengeluaran
            </div>

            <div class="chart-card-description">
                Perbandingan nilai pemasukan dan pengeluaran
                selama periode laporan.
            </div>


            <div class="chart-image-large">

                <img src="{{ $trendChartImage }}">

            </div>

        </div>


        <div class="chart-summary">

            <table>

                <tr>

                    <td>

                        <div class="mini-label">
                            TOTAL PEMASUKAN
                        </div>

                        <div class="mini-value income-text">
                            Rp
                            {{ number_format($totalIncome, 0, ',', '.') }}
                        </div>

                    </td>


                    <td>

                        <div class="mini-label">
                            TOTAL PENGELUARAN
                        </div>

                        <div class="mini-value expense-text">
                            Rp
                            {{ number_format($totalExpense, 0, ',', '.') }}
                        </div>

                    </td>


                    <td>

                        <div class="mini-label">
                            SALDO BERSIH
                        </div>

                        <div class="mini-value balance-text">
                            Rp
                            {{ number_format($netBalance, 0, ',', '.') }}
                        </div>

                    </td>

                </tr>

            </table>

        </div>


        <div class="charts-footer">
            Laporan Keuangan · Sistem Informasi Manajemen
        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- HALAMAN GRAFIK 2 --}}
    {{-- ========================================================= --}}

    <div class="chart-page">

        <div class="charts-header">

            <table class="charts-header-table">

                <tr>

                    <td class="charts-header-left">

                        <div class="charts-kicker">
                            ANALISIS KEUANGAN
                        </div>

                        <div class="charts-title">
                            Keuangan per Unit
                        </div>

                        <div class="charts-description">
                            Perbandingan pemasukan dan pengeluaran
                            berdasarkan unit organisasi.
                        </div>

                    </td>


                    <td class="charts-header-right">

                        <div class="charts-period">

                            <div class="charts-period-label">
                                PERIODE
                            </div>

                            <div class="charts-period-value">

                                {{ $startDate->translatedFormat('d F Y') }}

                                &ndash;

                                {{ $endDate->translatedFormat('d F Y') }}

                            </div>

                            <div class="charts-period-label" style="margin-top: 4px;">
                                UNIT
                            </div>

                            <div class="charts-period-value">

                                {{ $reportOrganization?->name ?? 'Semua Unit' }}

                            </div>

                        </div>

                    </td>

                </tr>

            </table>

        </div>


        <div class="chart-card-large">

            <div class="chart-card-title">
                Perbandingan Keuangan per Unit
            </div>

            <div class="chart-card-description">
                Perbandingan total pemasukan dan pengeluaran
                setiap unit organisasi.
            </div>


            <div class="chart-image-large">

                <img src="{{ $organizationChartImage }}">

            </div>

        </div>


        <div class="charts-footer">
            Laporan Keuangan · Sistem Informasi Manajemen
        </div>

    </div>


</body>

</html>
