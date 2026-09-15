<div class="modal fade" id="modalFilterPaymentReport" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content">

            <form method="GET" action="{{ route('admin.reports.payments') }}">

                <div class="modal-header">

                    <h5 class="modal-title">

                        <i class="bi bi-funnel me-1"></i>

                        Filter Laporan Pembayaran

                    </h5>

                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>

                </div>


                <div class="modal-body">

                    <div class="row g-3">

                        {{-- UNIT / LEMBAGA --}}
                        <div class="col-md-12">

                            <label class="form-label">
                                Unit / Lembaga
                            </label>

                            <select name="organization_id" class="form-select">

                                <option value="">
                                    Semua Unit
                                </option>

                                @foreach ($organizations as $organization)
                                    <option value="{{ $organization->id }}" @selected((string) $organizationId === (string) $organization->id)>
                                        {{ $organization->name }}
                                    </option>
                                @endforeach

                            </select>

                        </div>

                        {{-- STATUS --}}

                        <div class="col-md-6">

                            <label class="form-label">
                                Status
                            </label>

                            <select name="status" class="form-select">

                                <option value="">
                                    Semua Status
                                </option>

                                <option value="pending" @selected($status === 'pending')>
                                    Menunggu
                                </option>

                                <option value="confirmed" @selected($status === 'confirmed')>
                                    Dikonfirmasi
                                </option>

                                <option value="failed" @selected($status === 'failed')>
                                    Gagal
                                </option>

                                <option value="cancelled" @selected($status === 'cancelled')>
                                    Dibatalkan
                                </option>

                            </select>

                        </div>


                        {{-- METODE PEMBAYARAN --}}

                        <div class="col-md-6">

                            <label class="form-label">
                                Metode Pembayaran
                            </label>

                            <select name="payment_method" class="form-select">

                                <option value="">
                                    Semua Metode
                                </option>

                                <option value="cash" @selected($paymentMethod === 'cash')>
                                    Tunai
                                </option>

                                <option value="bank_transfer" @selected($paymentMethod === 'bank_transfer')>
                                    Transfer Bank
                                </option>

                                <option value="online" @selected($paymentMethod === 'online')>
                                    Online
                                </option>

                            </select>

                        </div>


                        {{-- TAHUN --}}

                        <div class="col-md-6">

                            <label class="form-label">
                                Tahun
                            </label>

                            <select name="year" class="form-select">

                                <option value="">
                                    Semua Tahun
                                </option>

                                @foreach ($years as $availableYear)
                                    <option value="{{ $availableYear }}" @selected((string) $year === (string) $availableYear)>
                                        {{ $availableYear }}
                                    </option>
                                @endforeach

                            </select>

                        </div>


                        {{-- BULAN --}}

                        <div class="col-md-6">

                            <label class="form-label">
                                Bulan
                            </label>

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

                            <select name="month" class="form-select">

                                <option value="">
                                    Semua Bulan
                                </option>

                                @foreach ($months as $monthNumber => $monthName)
                                    <option value="{{ $monthNumber }}" @selected((string) $month === (string) $monthNumber)>
                                        {{ $monthName }}
                                    </option>
                                @endforeach

                            </select>

                        </div>


                        {{-- TANGGAL DARI --}}

                        <div class="col-md-6">

                            <label class="form-label">
                                Tanggal Dari
                            </label>

                            <input type="date" name="date_from" value="{{ $dateFrom }}" class="form-control">

                        </div>


                        {{-- TANGGAL SAMPAI --}}

                        <div class="col-md-6">

                            <label class="form-label">
                                Tanggal Sampai
                            </label>

                            <input type="date" name="date_to" value="{{ $dateTo }}" class="form-control">

                        </div>


                        {{-- PENCARIAN --}}

                        <div class="col-12">

                            <label class="form-label">
                                Pencarian
                            </label>

                            <input type="text" name="search" value="{{ $search }}" class="form-control"
                                placeholder="Nomor pembayaran, NIS, atau nama siswa...">

                        </div>

                    </div>

                </div>


                <div class="modal-footer">

                    <a href="{{ route('admin.reports.payments') }}" class="btn btn-outline-secondary">

                        <i class="bi bi-arrow-counterclockwise me-1"></i>

                        Reset

                    </a>


                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Batal
                    </button>


                    <button type="submit" class="btn btn-primary">

                        <i class="bi bi-search me-1"></i>

                        Terapkan Filter

                    </button>

                </div>

            </form>

        </div>

    </div>
</div>
