<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <title>Laporan Pembayaran</title>

    <style>
        @page {
            margin: 20px 15px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;
            color: #222;
        }

        h1 {
            font-size: 16px;
            margin: 0 0 4px;
            text-align: center;
        }

        .subtitle {
            text-align: center;
            font-size: 10px;
            margin-bottom: 12px;
        }

        .filter {
            margin-bottom: 10px;
            font-size: 8px;
        }

        .filter strong {
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background-color: #eeeeee;
            border: 1px solid #999;
            padding: 5px 4px;
            text-align: center;
            font-weight: bold;
        }

        td {
            border: 1px solid #aaa;
            padding: 4px;
            vertical-align: top;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .amount {
            text-align: right;
            white-space: nowrap;
        }

        .status {
            text-align: center;
            white-space: nowrap;
        }

        .small {
            font-size: 8px;
            color: #555;
        }

        .total-row td {
            font-weight: bold;
            background-color: #f5f5f5;
        }

        .footer {
            margin-top: 10px;
            font-size: 8px;
            color: #666;
            text-align: right;
        }

        .allocation {
            margin-bottom: 2px;
        }
    </style>
</head>

<body>

    <h1>LAPORAN PEMBAYARAN</h1>

    <div class="subtitle">
        {{ $organizationName }}
    </div>

    @if (!empty($filterDescription))
        <div class="filter">
            <strong>Filter:</strong>
            {{ implode(' | ', $filterDescription) }}
        </div>
    @else
        <div class="filter">
            <strong>Filter:</strong> Semua Data
        </div>
    @endif

    <table>
        <thead>
            <tr>
                <th width="3%">No</th>
                <th width="8%">Tanggal</th>
                <th width="10%">Nomor</th>
                <th width="9%">Unit</th>
                <th width="14%">Siswa</th>
                <th width="7%">NIS</th>
                <th width="8%">Tagihan</th>
                <th width="8%">Periode</th>
                <th width="10%">Nominal</th>
                <th width="8%">Metode</th>
                <th width="8%">Status</th>
            </tr>
        </thead>

        <tbody>

            @forelse ($payments as $payment)

                @php
                    $allocations = $payment->allocations;
                @endphp

                @if ($allocations->isEmpty())
                    <tr>
                        <td class="text-center">
                            {{ $loop->iteration }}
                        </td>

                        <td>
                            {{ optional($payment->payment_date)->format('d/m/Y') }}
                        </td>

                        <td>
                            {{ $payment->payment_number }}
                        </td>

                        <td>
                            {{ $payment->organization?->name ?? '-' }}
                        </td>

                        <td>-</td>

                        <td>-</td>

                        <td>-</td>

                        <td>-</td>

                        <td class="amount">
                            Rp {{ number_format($payment->amount, 0, ',', '.') }}
                        </td>

                        <td>
                            @switch($payment->payment_method)
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
                                    {{ $payment->payment_method }}
                            @endswitch
                        </td>

                        <td class="status">
                            @switch($payment->status)
                                @case('pending')
                                    Menunggu Konfirmasi
                                @break

                                @case('confirmed')
                                    Dikonfirmasi
                                @break

                                @case('failed')
                                    Gagal
                                @break

                                @case('cancelled')
                                    Dibatalkan
                                @break

                                @default
                                    {{ $payment->status }}
                            @endswitch
                        </td>
                    </tr>
                @else
                    @foreach ($allocations as $allocation)
                        @php
                            $bill = $allocation->studentBill;
                            $studentAcademicYear = $bill?->studentAcademicYear;
                            $student = $studentAcademicYear?->student;
                            $billType = $bill?->billType;
                        @endphp

                        <tr>

                            <td class="text-center">
                                @if ($loop->first)
                                    {{ $payments->search(fn($item) => $item->id === $payment->id) + 1 }}
                                @endif
                            </td>

                            <td>
                                @if ($loop->first)
                                    {{ optional($payment->payment_date)->format('d/m/Y') }}
                                @endif
                            </td>

                            <td>
                                @if ($loop->first)
                                    {{ $payment->payment_number }}
                                @endif
                            </td>

                            <td>
                                @if ($loop->first)
                                    {{ $payment->organization?->name ?? '-' }}
                                @endif
                            </td>

                            <td>
                                {{ $student?->name ?? '-' }}
                            </td>

                            <td>
                                {{ $student?->nis ?? '-' }}
                            </td>

                            <td>
                                {{ $billType?->name ?? '-' }}
                            </td>

                            <td>
                                {{ $bill?->period ?? '-' }}
                            </td>

                            <td class="amount">
                                @if ($loop->first)
                                    Rp {{ number_format($payment->amount, 0, ',', '.') }}
                                @endif
                            </td>

                            <td>
                                @if ($loop->first)
                                    @switch($payment->payment_method)
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
                                            {{ $payment->payment_method }}
                                    @endswitch
                                @endif
                            </td>

                            <td class="status">
                                @if ($loop->first)
                                    @switch($payment->status)
                                        @case('pending')
                                            Menunggu Konfirmasi
                                        @break

                                        @case('confirmed')
                                            Dikonfirmasi
                                        @break

                                        @case('failed')
                                            Gagal
                                        @break

                                        @case('cancelled')
                                            Dibatalkan
                                        @break

                                        @default
                                            {{ $payment->status }}
                                    @endswitch
                                @endif
                            </td>

                        </tr>
                    @endforeach
                @endif

                @empty

                    <tr>
                        <td colspan="11" class="text-center">
                            Tidak ada data pembayaran.
                        </td>
                    </tr>

                @endforelse

            </tbody>

            @if ($payments->count())
                <tfoot>
                    <tr class="total-row">
                        <td colspan="8" class="text-right">
                            TOTAL PEMBAYARAN
                        </td>

                        <td class="amount">
                            Rp {{ number_format($payments->sum('amount'), 0, ',', '.') }}
                        </td>

                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            @endif

        </table>

        <div class="footer">
            Dicetak pada {{ now()->format('d/m/Y H:i') }}
        </div>

    </body>

    </html>
