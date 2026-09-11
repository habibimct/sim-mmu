<div class="modal fade"
     id="modalFilterDepositReport"
     tabindex="-1"
     aria-hidden="true">

    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content">

            <form method="GET"
                  action="{{ route('admin.reports.deposits') }}">

                <div class="modal-header">

                    <h5 class="modal-title">
                        <i class="bi bi-funnel me-1"></i>
                        Filter Laporan Setoran
                    </h5>

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close">
                    </button>

                </div>


                <div class="modal-body">

                    <div class="row g-3">

                        {{-- STATUS --}}
                        <div class="col-md-6">

                            <label class="form-label">
                                Status
                            </label>

                            <select name="status"
                                    class="form-select">

                                <option value="">
                                    Semua Status
                                </option>

                                <option value="pending"
                                    @selected($status === 'pending')>
                                    Menunggu Konfirmasi
                                </option>

                                <option value="confirmed"
                                    @selected($status === 'confirmed')>
                                    Dikonfirmasi
                                </option>

                                <option value="rejected"
                                    @selected($status === 'rejected')>
                                    Ditolak
                                </option>

                            </select>

                        </div>


                        {{-- TAHUN --}}
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


                        {{-- BULAN --}}
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


                        {{-- TANGGAL DARI --}}
                        <div class="col-md-6">

                            <label class="form-label">
                                Tanggal Dari
                            </label>

                            <input type="date"
                                   name="date_from"
                                   value="{{ $dateFrom }}"
                                   class="form-control">

                        </div>


                        {{-- TANGGAL SAMPAI --}}
                        <div class="col-md-6">

                            <label class="form-label">
                                Tanggal Sampai
                            </label>

                            <input type="date"
                                   name="date_to"
                                   value="{{ $dateTo }}"
                                   class="form-control">

                        </div>


                        {{-- PENCARIAN --}}
                        <div class="col-12">

                            <label class="form-label">
                                Pencarian
                            </label>

                            <input type="text"
                                   name="search"
                                   value="{{ $search }}"
                                   class="form-control"
                                   placeholder="Nama unit, tujuan, atau keterangan...">

                        </div>

                    </div>

                </div>


                <div class="modal-footer">

                    <a href="{{ route('admin.reports.deposits') }}"
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
