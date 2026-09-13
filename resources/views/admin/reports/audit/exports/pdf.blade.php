<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <title>Laporan Audit</title>

    <style>
        @page {
            margin: 20px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 8px;
            color: #222;
        }

        h1 {
            margin: 0;
            text-align: center;
            font-size: 18px;
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
            border-collapse: collapse;
        }

        .filter-info td {
            border: none;
            padding: 2px 4px;
        }

        .filter-label {
            font-weight: bold;
            width: 80px;
        }

        table.report {
            width: 100%;
            border-collapse: collapse;
        }

        table.report th {
            background: #e9ecef;
            text-align: center;
            font-weight: bold;
        }

        table.report th,
        table.report td {
            border: 1px solid #999;
            padding: 4px;
            vertical-align: top;
        }

        .center {
            text-align: center;
        }

        .badge {
            display: inline-block;
            padding: 3px 5px;
            border-radius: 3px;
            font-size: 7px;
        }

        .created {
            background: #d1e7dd;
            color: #0f5132;
        }

        .updated {
            background: #fff3cd;
            color: #664d03;
        }

        .deleted {
            background: #f8d7da;
            color: #842029;
        }

        .changes {
            font-size: 7px;
            line-height: 1.4;
        }

        .change-item {
            margin-bottom: 3px;
        }

        .footer {
            margin-top: 12px;
            text-align: right;
            font-size: 7px;
            color: #777;
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


    <h1>LAPORAN AUDIT</h1>

    <div class="subtitle">
        {{ $organizationName }}
    </div>


    {{-- Filter --}}
    <div class="filter-info">

        <table>

            <tr>

                <td class="filter-label">
                    Modul
                </td>

                <td>
                    @if ($logName === 'finance_deposit')
                        Setoran
                    @elseif ($logName === 'finance_transaction')
                        Transaksi Keuangan
                    @else
                        Semua Modul
                    @endif
                </td>

                <td class="filter-label">
                    Aktivitas
                </td>

                <td>
                    @if ($event === 'created')
                        Dibuat
                    @elseif ($event === 'updated')
                        Diperbarui
                    @elseif ($event === 'deleted')
                        Dihapus
                    @else
                        Semua Aktivitas
                    @endif
                </td>

            </tr>

            <tr>

                <td class="filter-label">
                    Tahun
                </td>

                <td>
                    {{ $year ?: 'Semua Tahun' }}
                </td>

                <td class="filter-label">
                    Bulan
                </td>

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

            </tr>

            <tr>

                <td class="filter-label">
                    Periode
                </td>

                <td colspan="3">

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

                    <td class="filter-label">
                        Pencarian
                    </td>

                    <td colspan="3">
                        {{ $search }}
                    </td>

                </tr>
            @endif

        </table>

    </div>


    {{-- Tabel --}}
    <table class="report">

        <thead>

            <tr>

                <th width="4%">
                    No
                </th>

                <th width="10%">
                    Waktu
                </th>

                <th width="14%">
                    Pengguna
                </th>

                <th width="11%">
                    Aktivitas
                </th>

                <th width="14%">
                    Modul
                </th>

                <th width="11%">
                    Data
                </th>

                <th width="14%">
                    Organisasi
                </th>

                <th width="22%">
                    Deskripsi
                </th>

            </tr>

        </thead>

        <tbody>

            @forelse ($activities as $index => $activity)

                @php

                    $properties = $activity->properties;

                    if ($properties instanceof \Illuminate\Support\Collection) {
                        $properties = $properties->toArray();
                    }

                    $properties = $properties ?? [];

                    $old = $properties['old'] ?? [];

                    $attributes = $properties['attributes'] ?? [];

                    $organizationId = $attributes['organization_id'] ?? ($old['organization_id'] ?? null);

                    $organization = $organizationId ? \App\Models\Organization::find($organizationId) : null;

                    $changes = [];

                    $fields = array_unique(array_merge(array_keys($old), array_keys($attributes)));

                    foreach ($fields as $field) {
                        $oldValue = $old[$field] ?? null;

                        $newValue = $attributes[$field] ?? null;

                        if ($activity->event === 'updated' && $oldValue === $newValue) {
                            continue;
                        }

                        $changes[] = [
                            'field' => $field,
                            'old' => $oldValue,
                            'new' => $newValue,
                        ];
                    }

                @endphp

                <tr>

                    <td class="center">
                        {{ $index + 1 }}
                    </td>

                    <td class="center">

                        {{ optional($activity->created_at)->format('d/m/Y') }}

                        <br>

                        {{ optional($activity->created_at)->format('H:i:s') }}

                    </td>

                    <td>

                        <strong>
                            {{ $activity->causer?->name ?? 'Sistem' }}
                        </strong>

                        @if ($activity->causer?->email)
                            <br>

                            <span style="font-size:7px;">
                                {{ $activity->causer->email }}
                            </span>
                        @endif

                    </td>

                    <td class="center">

                        @if ($activity->event === 'created')
                            <span class="badge created">
                                Dibuat
                            </span>
                        @elseif ($activity->event === 'updated')
                            <span class="badge updated">
                                Diperbarui
                            </span>
                        @elseif ($activity->event === 'deleted')
                            <span class="badge deleted">
                                Dihapus
                            </span>
                        @else
                            {{ $activity->event ?: '-' }}
                        @endif

                    </td>

                    <td>

                        @if ($activity->log_name === 'finance_deposit')
                            Setoran
                        @elseif ($activity->log_name === 'finance_transaction')
                            Transaksi Keuangan
                        @else
                            {{ $activity->log_name ?: '-' }}
                        @endif

                    </td>

                    <td class="center">

                        @if ($activity->subject_type)
                            {{ class_basename($activity->subject_type) }}

                            <br>

                            #{{ $activity->subject_id }}
                        @else
                            -
                        @endif

                    </td>

                    <td>

                        {{ $organization?->name ?? '-' }}

                    </td>

                    <td>

                        {{ $activity->description ?: '-' }}

                        @if (count($changes))
                            <div class="changes" style="margin-top:4px;">

                                <strong>
                                    Perubahan:
                                </strong>

                                @foreach ($changes as $change)
                                    <div class="change-item">

                                        {{ $change['field'] }}:

                                        {{ is_null($change['old']) ? '-' : (is_array($change['old']) ? json_encode($change['old']) : $change['old']) }}

                                        →

                                        {{ $change['new'] === null ? '-' : (is_array($change['new']) ? json_encode($change['new']) : $change['new']) }}

                                    </div>
                                @endforeach

                            </div>
                        @endif

                    </td>

                </tr>

            @empty

                <tr>

                    <td colspan="8" class="center">

                        Tidak ada data audit.

                    </td>

                </tr>

            @endforelse

        </tbody>

    </table>


    <div style="margin-top:10px;text-align:right;">

        <strong>
            Total Aktivitas:
        </strong>

        {{ $activities->count() }}

    </div>


    <div class="footer">

        Dicetak pada
        {{ now()->format('d/m/Y H:i') }}

    </div>

</body>

</html>
