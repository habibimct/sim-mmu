<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <title>
        Laporan Keuangan - {{ $organization->name }}
    </title>

    <style>
        @page {
            size: A4 landscape;
            margin: 15mm 12mm 15mm 12mm;
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
            max-width: 65px;
            max-height: 65px;
        }

        .kop-identitas {
            width: 50%;
            text-align: center;
            vertical-align: middle;
            padding-left: 8px;
        }

        .kop-identitas .induk {
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .kop-identitas .unit {
            font-size: 15px;
            font-weight: bold;
            margin-top: 2px;
        }

        .kop-identitas .alamat {
            margin-top: 4px;
            font-size: 8px;
            line-height: 1.4;
        }

        .kop-kontak {
            width: 25%;
            text-align: right;
            vertical-align: middle;
            font-size: 8px;
            line-height: 1.5;
        }

        .judul {
            text-align: center;
            margin-bottom: 3px;
            font-size: 14px;
            font-weight: bold;
        }

        .periode {
            text-align: center;
            font-size: 9px;
            margin-bottom: 14px;
        }

        .filter-info {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }

        .filter-info td {
            padding: 3px 5px;
            border: 1px solid #ddd;
        }

        .filter-info .label {
            width: 15%;
            font-weight: bold;
            background: #f3f3f3;
        }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .summary-table td {
            width: 25%;
            border: 1px solid #bbb;
            padding: 7px;
            text-align: center;
        }

        .summary-label {
            display: block;
            font-size: 8px;
            margin-bottom: 3px;
            color: #555;
        }

        .summary-value {
            display: block;
            font-size: 11px;
            font-weight: bold;
        }

        .section-title {
            font-size: 10px;
            font-weight: bold;
            margin: 10px 0 5px;
        }

        .transaction-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .transaction-table th,
        .transaction-table td {
            border: 1px solid #aaa;
            padding: 5px 4px;
            vertical-align: top;
        }

        .transaction-table th {
            background: #eee;
            text-align: center;
            font-weight: bold;
        }

        .transaction-table .tanggal {
            width: 9%;
        }

        .transaction-table .jenis {
            width: 10%;
        }

        .transaction-table .kategori {
            width: 17%;
        }

        .transaction-table .jumlah {
            width: 15%;
        }

        .transaction-table .metode {
            width: 12%;
        }

        .transaction-table .keterangan {
            width: 37%;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .footer {
            margin-top: 15px;
            font-size: 8px;
            color: #666;
        }

        .signature {
            width: 100%;
            margin-top: 25px;
            border-collapse: collapse;
        }

        .signature td {
            width: 50%;
            text-align: center;
            vertical-align: top;
        }

        .signature-space {
            height: 50px;
        }

        .empty {
            text-align: center;
            padding: 15px;
            color: #666;
        }
    </style>
</head>

<body>

    {{-- ========================================================= --}}
    {{-- KOP --}}
    {{-- ========================================================= --}}

    <div class="kop">

        <table class="kop-table">
            <tr>

                {{-- Logo Unit --}}
                <td class="kop-logo">

                    @if ($organization?->logo_path)
                        <img src="{{ public_path('storage/' . $organization->logo_path) }}" alt="Logo Unit">
                    @elseif ($induk?->logo_path)
                        <img src="{{ public_path('storage/' . $induk->logo_path) }}" alt="Logo PMUB">
                    @endif

                </td>

                {{-- Identitas --}}
                <td class="kop-identitas">

                    <div class="induk">
                        {{ $induk->name }}
                    </div>

                    <div class="unit">
                        {{ $organization->name }}
                    </div>

                    @if ($organization->address)
                        <div class="alamat">
                            {{ $organization->address }}
                        </div>
                    @endif

                </td>

                {{-- Kontak --}}
                <td class="kop-kontak">

                    @if ($organization->phone)
                        Telp: {{ $organization->phone }}<br>
                    @endif

                    @if ($organization->email)
                        Email: {{ $organization->email }}<br>
                    @endif

                    @if ($organization->website)
                        {{ $organization->website }}
                    @endif

                </td>

            </tr>
        </table>

    </div>


    {{-- ========================================================= --}}
    {{-- JUDUL --}}
    {{-- ========================================================= --}}

    <div class="judul">
        LAPORAN KEUANGAN
    </div>

    <div class="periode">
        Periode {{ \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') }}
        s.d.
        {{ \Carbon\Carbon::parse($dateTo)->format('d/m/Y') }}
    </div>


    {{-- ========================================================= --}}
    {{-- INFORMASI FILTER --}}
    {{-- ========================================================= --}}

    <table class="filter-info">

        <tr>
            <td class="label">Unit</td>
            <td>{{ $organization->name }}</td>
        </tr>

        <tr>
            <td class="label">Jenis Laporan</td>
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
                        Semua Transaksi
                @endswitch
            </td>
        </tr>

    </table>


    {{-- ========================================================= --}}
    {{-- RINGKASAN --}}
    {{-- ========================================================= --}}

    <div class="section-title">
        Ringkasan Keuangan
    </div>

    <table class="summary-table">

        <tr>

            <td>
                <span class="summary-label">
                    TOTAL PEMASUKAN
                </span>

                <span class="summary-value">
                    Rp {{ number_format($totalIncome, 0, ',', '.') }}
                </span>
            </td>

            <td>
                <span class="summary-label">
                    TOTAL PENGELUARAN
                </span>

                <span class="summary-value">
                    Rp {{ number_format($totalExpense, 0, ',', '.') }}
                </span>
            </td>

            <td>
                <span class="summary-label">
                    TOTAL SETORAN
                </span>

                <span class="summary-value">
                    Rp {{ number_format($totalDeposit, 0, ',', '.') }}
                </span>
            </td>

            <td>
                <span class="summary-label">
                    SALDO
                </span>

                <span class="summary-value">
                    Rp {{ number_format($netBalance, 0, ',', '.') }}
                </span>
            </td>

        </tr>

    </table>


    {{-- ========================================================= --}}
    {{-- DETAIL TRANSAKSI --}}
    {{-- ========================================================= --}}

    <div class="section-title">
        Detail Transaksi
    </div>

    <table class="transaction-table">

        <thead>
            <tr>

                <th class="tanggal">
                    Tanggal
                </th>

                <th class="jenis">
                    Jenis
                </th>

                <th class="kategori">
                    Kategori
                </th>

                <th class="jumlah">
                    Jumlah
                </th>

                <th class="metode">
                    Metode
                </th>

                <th class="keterangan">
                    Keterangan
                </th>

            </tr>
        </thead>

        <tbody>

            @forelse ($transactions as $transaction)
                @php
                    $isDeposit = $transaction->source_type === 'deposit';
                @endphp

                <tr>

                    <td class="text-center">
                        {{ \Carbon\Carbon::parse($transaction->transaction_date)->format('d/m/Y') }}
                    </td>

                    <td>
                        @if ($isDeposit)
                            Setoran
                        @elseif ($transaction->type === 'income')
                            Pemasukan
                        @else
                            Pengeluaran
                        @endif
                    </td>

                    <td>
                        {{ $transaction->category ?: '-' }}
                    </td>

                    <td class="text-right">
                        Rp {{ number_format((float) $transaction->amount, 0, ',', '.') }}
                    </td>

                    <td>
                        {{ $transaction->payment_method ?: '-' }}
                    </td>

                    <td>
                        {{ $transaction->description ?: '-' }}
                    </td>

                </tr>

            @empty

                <tr>
                    <td colspan="6" class="empty">
                        Tidak ada transaksi pada periode yang dipilih.
                    </td>
                </tr>
            @endforelse

        </tbody>

    </table>


    {{-- ========================================================= --}}
    {{-- FOOTER --}}
    {{-- ========================================================= --}}

    <div class="footer">
        Dicetak pada
        {{ now()->format('d/m/Y H:i') }}
        WIB
    </div>


    {{-- ========================================================= --}}
    {{-- TANDA TANGAN --}}
    {{-- ========================================================= --}}

    <table class="signature">

        <tr>

            <td>
                Mengetahui,<br>
                Kepala<br>
                {{ $organization->name }}

                <div class="signature-space"></div>

                <strong>{{ $namaKepalaUnit }}</strong>
            </td>

            <td>
                Dibuat oleh,<br>
                Bendahara<br>
                {{ $organization->name }}

                <div class="signature-space"></div>

                <strong>{{ $namaBendaharaUnit }}</strong>
            </td>

        </tr>

    </table>

</body>

</html>
