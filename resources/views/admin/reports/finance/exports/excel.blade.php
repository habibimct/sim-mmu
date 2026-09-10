<table>

    <tr>
        <th colspan="8">
            LAPORAN KEUANGAN
        </th>
    </tr>

    <tr>
        <td>Periode</td>
        <td colspan="7">
            {{ \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') }}
            s.d.
            {{ \Carbon\Carbon::parse($dateTo)->format('d/m/Y') }}
        </td>
    </tr>

    <tr>
        <td>Unit</td>
        <td colspan="7">
            {{ $organization?->name ?? 'Semua Unit' }}
        </td>
    </tr>

    <tr>
        <td>Jenis</td>
        <td colspan="7">
            @if ($type === 'income')
                Pemasukan
            @elseif ($type === 'expense')
                Pengeluaran
            @else
                Semua Jenis Transaksi
            @endif
        </td>
    </tr>

    <tr>
        <td>Kategori</td>
        <td colspan="7">
            {{ $category === 'all'
                ? 'Semua Kategori'
                : $category }}
        </td>
    </tr>

    <tr>
        <td></td>
    </tr>

    {{-- Ringkasan --}}
    <tr>
        <th>Total Pemasukan</th>
        <th>Total Pengeluaran</th>
        <th>Saldo Bersih</th>
    </tr>

    <tr>
        <td>
            {{ $totalIncome }}
        </td>

        <td>
            {{ $totalExpense }}
        </td>

        <td>
            {{ $netBalance }}
        </td>
    </tr>

    <tr>
        <td></td>
    </tr>

    {{-- Rekap Organisasi --}}
    <tr>
        <th colspan="4">
            REKAP KEUANGAN PER UNIT
        </th>
    </tr>

    <tr>
        <th>No</th>
        <th>Unit</th>
        <th>Pemasukan</th>
        <th>Pengeluaran</th>
        <th>Saldo</th>
    </tr>

    @foreach ($organizationSummary as $index => $row)

        <tr>

            <td>
                {{ $index + 1 }}
            </td>

            <td>
                {{ $row->organization?->name ?? '-' }}
            </td>

            <td>
                {{ $row->total_income }}
            </td>

            <td>
                {{ $row->total_expense }}
            </td>

            <td>
                {{ $row->net_balance }}
            </td>

        </tr>

    @endforeach

    <tr>
        <td></td>
    </tr>

    {{-- Detail --}}
    <tr>
        <th colspan="8">
            DETAIL TRANSAKSI
        </th>
    </tr>

    <tr>
        <th>No</th>
        <th>Tanggal</th>
        <th>Unit</th>
        <th>Jenis</th>
        <th>Kategori</th>
        <th>Keterangan</th>
        <th>Metode</th>
        <th>Nominal</th>
    </tr>

    @foreach ($transactions as $index => $transaction)

        <tr>

            <td>
                {{ $index + 1 }}
            </td>

            <td>
                {{ $transaction->transaction_date?->format('d/m/Y') }}
            </td>

            <td>
                {{ $transaction->organization?->name ?? '-' }}
            </td>

            <td>
                {{ $transaction->type === 'income'
                    ? 'Pemasukan'
                    : 'Pengeluaran' }}
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

            <td>
                {{ $transaction->amount }}
            </td>

        </tr>

    @endforeach

</table>
