<div class="card card-outline card-primary">

    <div class="card-header">

        <h3 class="card-title">
            <i class="fas fa-list mr-1"></i>
            Detail Transaksi
        </h3>

    </div>

    <div class="card-body p-0">

        <div class="table-responsive">

            <table class="table table-bordered table-hover mb-0">

                <thead class="thead-light">

                    <tr>

                        <th width="60">
                            #
                        </th>

                        <th>
                            Tanggal
                        </th>

                        <th>
                            Unit
                        </th>

                        <th>
                            Jenis
                        </th>

                        <th>
                            Kategori
                        </th>

                        <th>
                            Keterangan
                        </th>

                        <th>
                            Metode
                        </th>

                        <th class="text-right">
                            Nominal
                        </th>

                    </tr>

                </thead>

                <tbody>

                    @forelse ($transactions as $transaction)

                        <tr>

                            <td>
                                {{ $transactions->firstItem() + $loop->index }}
                            </td>

                            <td>
                                {{ $transaction->transaction_date?->format('d/m/Y') }}
                            </td>

                            <td>
                                {{ $transaction->organization?->name ?? '-' }}
                            </td>

                            <td>

                                @if ($transaction->type === 'income')

                                    <span class="badge badge-success">
                                        Pemasukan
                                    </span>

                                @else

                                    <span class="badge badge-danger">
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

                            <td class="text-right font-weight-bold">

                                Rp
                                {{ number_format(
                                    $transaction->amount,
                                    0,
                                    ',',
                                    '.'
                                ) }}

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="8"
                                class="text-center text-muted py-4"
                            >
                                Tidak ada transaksi
                                pada periode yang dipilih.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>


    @if ($transactions->hasPages())
        <div class="d-flex justify-content-end align-items-center p-3 border-top">

            <div>
            {{ $transactions->onEachSide(1)->links('pagination::bootstrap-5') }}
            </div>

        </div>
    @endif

</div>
