    {{-- ==========================================================
    DAFTAR TRANSAKSI
    =========================================================== --}}

    <div class="card">
        <div class="card-header">

            <h3 class="card-title">

                <i class="bi bi-list-ul me-1"></i>

                Daftar Transaksi

            </h3>

        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-bordered mb-0">
                    <thead class="table-light">
                        <tr>

                            <th width="55">
                                No.
                            </th>

                            <th width="120">
                                Tanggal
                            </th>

                            <th width="120">
                                Jenis
                            </th>

                            <th>
                                Unit
                            </th>

                            <th>
                                Kategori
                            </th>

                            <th width="170" class="text-end">
                                Jumlah
                            </th>

                            <th width="140">
                                Metode
                            </th>

                            <th>
                                Keterangan
                            </th>

                            <th style="min-width: 270px;">
                                Alasan
                            </th>

                            <th width="140" class="text-center">
                                Aksi
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

                                    {{ $transaction->transaction_date->format('d/m/Y') }}

                                </td>


                                <td>

                                    @if ($transaction->type === 'income')
                                        <span class="badge bg-success">

                                            Pemasukan

                                        </span>
                                    @else
                                        <span class="badge bg-danger">

                                            Pengeluaran

                                        </span>
                                    @endif

                                </td>


                                <td>

                                    {{ $transaction->organization->name }}

                                </td>


                                <td>

                                    {{ $transaction->category }}

                                </td>


                                <td class="text-end fw-bold">

                                    Rp
                                    {{ number_format($transaction->amount, 0, ',', '.') }}

                                </td>

                                @php
                                    $paymentMethodLabels = [
                                        'cash' => 'Tunai / Offline',
                                        'bank_transfer' => 'Transfer Bank',
                                        'online' => 'Online Lainnya',
                                    ];
                                @endphp

                                <td>
                                    {{ $paymentMethodLabels[$transaction->payment_method] ?? '-' }}
                                </td>


                                <td>
                                    {{ $transaction->description ?: '-' }}
                                </td>

                                <td>
                                    @if ($transaction->status === 'cancelled')
                                        <span class="text-danger">
                                            {{ $transaction->cancellation_reason ?: '-' }}
                                        </span>
                                    @elseif ($transaction->status === 'rejected')
                                        <span class="text-muted">
                                            {{ $transaction->rejection_reason ?? '-' }}
                                        </span>
                                    @else
                                        -
                                    @endif
                                </td>

                                <td class="text-center">

                                    @if ($transaction->status === 'rejected')
                                        <span class="text-muted small">
                                            <i class="bi bi-x-circle me-1"></i>
                                            Telah ditolak
                                        </span>
                                    @elseif ($transaction->status === 'cancelled')
                                        <span class="text-muted small">
                                            <i class="bi bi-slash-circle me-1"></i>
                                            Telah dibatalkan
                                        </span>
                                    @elseif ($transaction->status === 'pending')
                                        <span class="text-muted small">
                                            <i class="bi bi-clock me-1"></i>
                                            Menunggu konfirmasi
                                        </span>
                                    @elseif ($transaction->status === 'confirmed')
                                        @if (in_array($transaction->source_type, ['deposit', 'student_payment'], true))
                                            <span class="text-muted small">
                                                <i class="bi bi-lock me-1"></i>
                                                Tidak dapat dibatalkan
                                            </span>
                                        @elseif ($transaction->source_type === 'normal')
                                            @can('cancel', $transaction)
                                                <button type="button" class="btn btn-sm btn-outline-danger"
                                                    data-bs-toggle="modal" data-bs-target="#modalBatalkanTransaksi"
                                                    data-cancel-url="{{ route('admin.finance.transactions.cancel', ['transaction' => $transaction->id]) }}"
                                                    data-transaction-amount="{{ number_format($transaction->amount, 0, ',', '.') }}"
                                                    data-transaction-type="{{ $transaction->type === 'income' ? 'Pemasukan' : 'Pengeluaran' }}"
                                                    data-transaction-category="{{ $transaction->category }}">
                                                    <i class="bi bi-x-circle me-1"></i>
                                                    Batalkan
                                                </button>
                                            @else
                                                <span class="text-muted small">
                                                    Tidak dapat dibatalkan
                                                </span>
                                            @endcan
                                        @endif
                                    @endif

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="10" class="text-center text-muted py-5">

                                    <i class="bi bi-inbox fs-3 d-block mb-2"></i>

                                    Belum ada transaksi keuangan.

                                </td>

                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>


        @if ($transactions->hasPages())
            <div class="card-footer">
                <div class="d-flex justify-content-end">
                    {{ $transactions->onEachSide(1)->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif

    </div>
