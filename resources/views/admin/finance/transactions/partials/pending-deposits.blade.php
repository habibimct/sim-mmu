    {{-- ==========================================================
    SETORAN MENUNGGU KONFIRMASI
    ========================================================== --}}

    @if ($pendingDeposits->count())

        <div class="card mt-4">

            <div class="card-header">

                <h3 class="card-title">

                    <i class="bi bi-hourglass-split me-1"></i>

                    Setoran Menunggu Konfirmasi

                </h3>

                <div class="card-tools">

                    <span class="badge bg-warning text-dark">

                        {{ $pendingDeposits->count() }} setoran

                    </span>

                </div>

            </div>


            <div class="card-body p-0">

                <div class="table-responsive">

                    <table class="table table-hover table-bordered mb-0">

                        <thead class="table-light">

                            <tr>

                                <th width="120">
                                    Tanggal
                                </th>

                                <th>
                                    Unit Pengirim
                                </th>

                                <th>
                                    Tujuan
                                </th>

                                <th width="160" class="text-end">
                                    Jumlah
                                </th>

                                <th width="150">
                                    Metode
                                </th>

                                <th width="130" class="text-center">
                                    Status
                                </th>

                                <th width="220" class="text-center">
                                    Aksi
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            @foreach ($pendingDeposits as $deposit)
                                <tr>

                                    {{-- Tanggal --}}

                                    <td>

                                        {{ $deposit->deposit_date->format('d/m/Y') }}

                                    </td>


                                    {{-- Pengirim --}}

                                    <td>

                                        <strong>
                                            {{ $deposit->organization->name }}
                                        </strong>

                                    </td>


                                    {{-- Tujuan --}}

                                    <td>

                                        {{ $deposit->targetOrganization->name }}

                                    </td>


                                    {{-- Jumlah --}}

                                    <td class="text-end">

                                        Rp
                                        {{ number_format($deposit->amount, 0, ',', '.') }}

                                    </td>


                                    {{-- Metode --}}

                                    <td>

                                        @switch($deposit->payment_method)
                                            @case('cash')
                                                Tunai / Offline
                                            @break

                                            @case('bank_transfer')
                                                Transfer Bank
                                            @break

                                            @case('online')
                                                Online
                                            @break
                                        @endswitch

                                    </td>


                                    {{-- Status --}}

                                    <td class="text-center">

                                        <span class="badge bg-warning text-dark">

                                            Menunggu Konfirmasi

                                        </span>

                                    </td>


                                    {{-- Aksi --}}

                                    <td class="text-center">

                                        @can('confirm', $deposit)
                                            <div class="d-flex justify-content-center gap-1">

                                                {{-- Lihat Bukti --}}

                                                @if ($deposit->proof_path)
                                                    <a href="{{ asset('storage/' . $deposit->proof_path) }}" target="_blank"
                                                        class="btn btn-sm btn-info" title="Lihat Bukti">

                                                        <i class="bi bi-image"></i>

                                                    </a>
                                                @endif


                                                {{-- Konfirmasi --}}

                                                <form method="POST"
                                                    action="{{ route('admin.finance.deposits.confirm', $deposit) }}"
                                                    class="d-inline">

                                                    @csrf

                                                    <button type="submit" class="btn btn-sm btn-success"
                                                        onclick="return confirm('Konfirmasi setoran ini? Setelah dikonfirmasi, transaksi pengeluaran pada unit dan pemasukan pada parent akan dibuat.')">

                                                        <i class="bi bi-check-lg"></i>
                                                    </button>

                                                </form>


                                                {{-- Tolak --}}

                                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                                    data-bs-target="#modalTolakSetoran{{ $deposit->id }}">

                                                    <i class="bi bi-x-lg"></i>
                                                </button>

                                            </div>
                                        @else
                                            <span class="text-muted">

                                                Menunggu parent

                                            </span>
                                        @endcan

                                    </td>

                                </tr>
                            @endforeach

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    @endif
