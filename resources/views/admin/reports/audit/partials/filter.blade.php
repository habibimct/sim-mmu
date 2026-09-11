<div class="modal fade" id="modalFilterAuditReport" tabindex="-1" aria-hidden="true">

    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content">

            <form method="GET"
                  action="{{ route('admin.reports.audit') }}">

                <div class="modal-header">

                    <h5 class="modal-title">
                        <i class="bi bi-funnel me-1"></i>
                        Filter Laporan Audit
                    </h5>

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close">
                    </button>

                </div>


                <div class="modal-body">

                    <div class="row g-3">

                        {{-- Modul --}}
                        <div class="col-md-6">

                            <label class="form-label">
                                Modul
                            </label>

                            <select name="log_name"
                                    class="form-select">

                                <option value="">
                                    Semua Modul
                                </option>

                                <option value="finance_deposit"
                                    @selected($logName === 'finance_deposit')>
                                    Setoran
                                </option>

                                <option value="finance_transaction"
                                    @selected($logName === 'finance_transaction')>
                                    Transaksi Keuangan
                                </option>

                            </select>

                        </div>


                        {{-- Aktivitas --}}
                        <div class="col-md-6">

                            <label class="form-label">
                                Aktivitas
                            </label>

                            <select name="event"
                                    class="form-select">

                                <option value="">
                                    Semua Aktivitas
                                </option>

                                <option value="created"
                                    @selected($event === 'created')>
                                    Dibuat
                                </option>

                                <option value="updated"
                                    @selected($event === 'updated')>
                                    Diperbarui
                                </option>

                                <option value="deleted"
                                    @selected($event === 'deleted')>
                                    Dihapus
                                </option>

                            </select>

                        </div>


                        {{-- Tahun --}}
                        <div class="col-md-3">

                            <label class="form-label">
                                Tahun
                            </label>

                            <select name="year"
                                    class="form-select">

                                <option value="">
                                    Semua Tahun
                                </option>

                                @foreach ($years as $item)

                                    <option value="{{ $item }}"
                                        @selected((string) $year === (string) $item)>
                                        {{ $item }}
                                    </option>

                                @endforeach

                            </select>

                        </div>


                        {{-- Bulan --}}
                        <div class="col-md-3">

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

                            <select name="month"
                                    class="form-select">

                                <option value="">
                                    Semua Bulan
                                </option>

                                @foreach ($months as $number => $name)

                                    <option value="{{ $number }}"
                                        @selected((string) $month === (string) $number)>
                                        {{ $name }}
                                    </option>

                                @endforeach

                            </select>

                        </div>


                        {{-- Tanggal Dari --}}
                        <div class="col-md-3">

                            <label class="form-label">
                                Tanggal Dari
                            </label>

                            <input type="date"
                                   name="date_from"
                                   value="{{ $dateFrom }}"
                                   class="form-control">

                        </div>


                        {{-- Tanggal Sampai --}}
                        <div class="col-md-3">

                            <label class="form-label">
                                Tanggal Sampai
                            </label>

                            <input type="date"
                                   name="date_to"
                                   value="{{ $dateTo }}"
                                   class="form-control">

                        </div>


                        {{-- Pencarian --}}
                        <div class="col-12">

                            <label class="form-label">
                                Pencarian
                            </label>

                            <input type="text"
                                   name="search"
                                   value="{{ $search }}"
                                   class="form-control"
                                   placeholder="Deskripsi, modul, atau aktivitas...">

                        </div>

                    </div>

                </div>


                <div class="modal-footer">

                    <a href="{{ route('admin.reports.audit') }}"
                       class="btn btn-outline-secondary">

                        <i class="bi bi-arrow-counterclockwise"></i>
                        Reset

                    </a>

                    <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">

                        Batal

                    </button>

                    <button type="submit"
                            class="btn btn-primary">

                        <i class="bi bi-check-lg"></i>
                        Terapkan Filter

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>
