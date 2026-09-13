<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <title>Laporan Setoran</title>

    <style>
        @page {
            margin: 20px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;
            color: #222;
        }

        h1 {
            margin: 0;
            font-size: 18px;
            text-align: center;
        }

        .subtitle {
            text-align: center;
            margin-top: 4px;
            font-size: 11px;
        }

        .filter-info {
            margin-top: 12px;
            margin-bottom: 12px;
            padding: 7px;
            border: 1px solid #ddd;
            background: #f8f9fa;
        }

        .filter-info table {
            width: 100%;
            border: none;
        }

        .filter-info td {
            border: none;
            padding: 2px 4px;
        }

        .filter-label {
            font-weight: bold;
            width: 90px;
        }

        table.report {
            width: 100%;
            border-collapse: collapse;
        }

        table.report th {
            background: #e9ecef;
            font-weight: bold;
            text-align: center;
        }

        table.report th,
        table.report td {
            border: 1px solid #999;
            padding: 5px;
            vertical-align: middle;
        }

        table.report td.center {
            text-align: center;
        }

        table.report td.right {
            text-align: right;
        }

        .badge {
            display: inline-block;
            padding: 3px 5px;
            border-radius: 3px;
            font-size: 8px;
        }

        .pending {
            background: #fff3cd;
            color: #664d03;
        }

        .confirmed {
            background: #d1e7dd;
            color: #0f5132;
        }

        .rejected {
            background: #f8d7da;
            color: #842029;
        }

        .proof-section {
            page-break-before: always;
        }

        .proof-title {
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .proof-card {
            border: 1px solid #aaa;
            padding: 8px;
            margin-bottom: 12px;

            /* Jangan pecah satu bukti ke dua halaman */
            page-break-inside: avoid;
            break-inside: avoid;
        }

        .proof-image {
            max-width: 500px;
            max-height: 400px;

            width: auto;
            height: auto;

            display: block;
            margin: 8px auto 0;

            page-break-inside: avoid;
            break-inside: avoid;
        }

        .proof-info {
            margin-bottom: 8px;
        }

        .proof-info table {
            width: 100%;
            border-collapse: collapse;
        }

        .proof-info td {
            border: none;
            padding: 2px;
        }

        .proof-label {
            width: 100px;
            font-weight: bold;
        }

        .proof-image {
            max-width: 500px;
            max-height: 550px;
        }

        .no-proof {
            color: #777;
            font-style: italic;
        }

        .footer {
            margin-top: 15px;
            text-align: right;
            color: #777;
            font-size: 8px;
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


    <h1>LAPORAN SETORAN</h1>

    <div class="subtitle">
        {{ $organizationName }}
    </div>


    {{-- =========================================================
         FILTER
    ========================================================== --}}

    <div class="filter-info">
        <table>
            <tr>
                <td class="filter-label">Status</td>
                <td>
                    @if ($status === 'pending')
                        Menunggu Konfirmasi
                    @elseif ($status === 'confirmed')
                        Dikonfirmasi
                    @elseif ($status === 'rejected')
                        Ditolak
                    @else
                        Semua Status
                    @endif
                </td>

                <td class="filter-label">Tahun</td>
                <td>{{ $year ?: 'Semua Tahun' }}</td>
            </tr>

            <tr>
                <td class="filter-label">Bulan</td>
                <td>
                    @php
                        $months = [
                            1 => 'Januari',
                            2 => 'Februari',
                            3 => 'Maret',
                            4 => 'April',
                            5 => 'Mei',
                            6 => 'Juni',
                            7 => 'Juli',
                            8 => 'Agustus',
                            9 => 'September',
                            10 => 'Oktober',
                            11 => 'November',
                            12 => 'Desember',
                        ];
                    @endphp

                    {{ $month ? $months[(int) $month] ?? $month : 'Semua Bulan' }}
                </td>

                <td class="filter-label">Periode</td>
                <td>
                    @if ($dateFrom || $dateTo)
                        {{ $dateFrom ?: '...' }}
                        s/d
                        {{ $dateTo ?: '...' }}
                    @else
                        Semua Tanggal
                    @endif
                </td>
            </tr>

            @if ($search)
                <tr>
                    <td class="filter-label">Pencarian</td>
                    <td colspan="3">
                        {{ $search }}
                    </td>
                </tr>
            @endif
        </table>
    </div>


    {{-- =========================================================
         TABEL LAPORAN
    ========================================================== --}}

    <table class="report">

        <thead>
            <tr>
                <th width="4%">No</th>
                <th width="8%">Tanggal</th>
                <th width="17%">Unit Pengirim</th>
                <th width="17%">Tujuan</th>
                <th width="13%">Nominal</th>
                <th width="11%">Metode</th>
                <th width="11%">Status</th>
                <th width="19%">Keterangan</th>
            </tr>
        </thead>

        <tbody>

            @forelse ($deposits as $index => $deposit)
                <tr>

                    <td class="center">
                        {{ $index + 1 }}
                    </td>

                    <td class="center">
                        {{ optional($deposit->deposit_date)->format('d/m/Y') }}
                    </td>

                    <td>
                        {{ $deposit->organization?->name ?? '-' }}
                    </td>

                    <td>
                        {{ $deposit->targetOrganization?->name ?? '-' }}
                    </td>

                    <td class="right">
                        Rp {{ number_format($deposit->amount, 0, ',', '.') }}
                    </td>

                    <td class="center">
                        @switch($deposit->payment_method)
                            @case('cash')
                                Tunai
                            @break

                            @case('bank_transfer')
                                Transfer Bank
                            @break

                            @case('online')
                                Online
                            @break

                            @default
                                {{ ucfirst($deposit->payment_method) }}
                        @endswitch
                    </td>

                    <td class="center">

                        @if ($deposit->status === 'confirmed')
                            <span class="badge confirmed">
                                Dikonfirmasi
                            </span>
                        @elseif ($deposit->status === 'pending')
                            <span class="badge pending">
                                Menunggu Konfirmasi
                            </span>
                        @elseif ($deposit->status === 'rejected')
                            <span class="badge rejected">
                                Ditolak
                            </span>
                        @else
                            {{ ucfirst($deposit->status) }}
                        @endif

                    </td>

                    <td>
                        {{ $deposit->description ?: '-' }}
                    </td>

                </tr>

                @empty

                    <tr>
                        <td colspan="8" class="center">
                            Tidak ada data setoran.
                        </td>
                    </tr>
                @endforelse

            </tbody>

        </table>


        {{-- =========================================================
         RINGKASAN
    ========================================================== --}}

        @if ($deposits->count())
            <div style="margin-top: 10px; text-align: right;">

                <strong>
                    Total Setoran:
                </strong>

                {{ $deposits->count() }}

                transaksi

                &nbsp;&nbsp;

                <strong>
                    Total Nominal:
                </strong>

                Rp
                {{ number_format($deposits->sum('amount'), 0, ',', '.') }}

            </div>
        @endif


        {{-- =========================================================
         BUKTI SETORAN
    ========================================================== --}}

        @php
            $depositsWithProof = $deposits->filter(fn($deposit) => !empty($deposit->proof_base64))->values();
        @endphp

        @if ($depositsWithProof->count())

            <div class="proof-section">

                <h2 class="proof-title">
                    BUKTI SETORAN
                </h2>

                @foreach ($depositsWithProof as $deposit)
                    <div class="proof-card">

                        <div class="proof-info">

                            <table>

                                <tr>
                                    <td class="proof-label">
                                        Tanggal
                                    </td>
                                    <td>
                                        {{ optional($deposit->deposit_date)->format('d/m/Y') }}
                                    </td>
                                </tr>

                                <tr>
                                    <td class="proof-label">
                                        Unit Pengirim
                                    </td>
                                    <td>
                                        {{ $deposit->organization?->name ?? '-' }}
                                    </td>
                                </tr>

                                <tr>
                                    <td class="proof-label">
                                        Tujuan
                                    </td>
                                    <td>
                                        {{ $deposit->targetOrganization?->name ?? '-' }}
                                    </td>
                                </tr>

                                <tr>
                                    <td class="proof-label">
                                        Nominal
                                    </td>
                                    <td>
                                        Rp {{ number_format($deposit->amount, 0, ',', '.') }}
                                    </td>
                                </tr>

                                <tr>
                                    <td class="proof-label">
                                        Status
                                    </td>
                                    <td>
                                        {{ ucfirst($deposit->status) }}
                                    </td>
                                </tr>

                                @if ($deposit->proof_original_name)
                                    <tr>
                                        <td class="proof-label">
                                            File
                                        </td>
                                        <td>
                                            {{ $deposit->proof_original_name }}
                                        </td>
                                    </tr>
                                @endif

                            </table>

                        </div>

                        <div style="text-align: center;">

                            <img src="{{ $deposit->proof_base64 }}" class="proof-image">

                        </div>

                    </div>
                @endforeach

            </div>

        @endif


        <div class="footer">
            Dicetak pada {{ now()->format('d/m/Y H:i') }}
        </div>

    </body>

    </html>
