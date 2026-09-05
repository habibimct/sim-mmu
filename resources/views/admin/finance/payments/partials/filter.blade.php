{{-- =========================================================
MODAL FILTER PEMBAYARAN
========================================================= --}}

<div
    class="modal fade"
    id="modalFilterPayments"
    tabindex="-1"
    aria-labelledby="modalFilterPaymentsLabel"
    aria-hidden="true"
>
    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content">

            {{-- =====================================================
            HEADER
            ====================================================== --}}

            <div class="modal-header">

                <div>

                    <h5
                        class="modal-title"
                        id="modalFilterPaymentsLabel"
                    >
                        Filter Pembayaran
                    </h5>

                    <p class="text-muted small mb-0 mt-1">
                        Pilih kriteria untuk menyaring daftar pembayaran.
                    </p>

                </div>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Tutup"
                ></button>

            </div>


            {{-- =====================================================
            FORM
            ====================================================== --}}

            <form
                method="GET"
                action="{{ route('admin.finance.payments.index') }}"
            >

                <div class="modal-body">

                    <div class="row g-3">

                        {{-- =================================================
                        STATUS
                        ================================================== --}}

                        <div class="col-md-6">

                            <label
                                for="filter_payment_status"
                                class="form-label"
                            >
                                Status
                            </label>

                            <select
                                name="status"
                                id="filter_payment_status"
                                class="form-select"
                            >

                                <option value="">
                                    Semua Status
                                </option>

                                <option
                                    value="pending"
                                    @selected($status === 'pending')
                                >
                                    Menunggu
                                </option>

                                <option
                                    value="confirmed"
                                    @selected($status === 'confirmed')
                                >
                                    Dikonfirmasi
                                </option>

                                <option
                                    value="failed"
                                    @selected($status === 'failed')
                                >
                                    Gagal
                                </option>

                                <option
                                    value="cancelled"
                                    @selected($status === 'cancelled')
                                >
                                    Dibatalkan
                                </option>

                            </select>

                        </div>


                        {{-- =================================================
                        METODE PEMBAYARAN
                        ================================================== --}}

                        <div class="col-md-6">

                            <label
                                for="filter_payment_method"
                                class="form-label"
                            >
                                Metode Pembayaran
                            </label>

                            <select
                                name="payment_method"
                                id="filter_payment_method"
                                class="form-select"
                            >

                                <option value="">
                                    Semua Metode
                                </option>

                                <option
                                    value="cash"
                                    @selected($paymentMethod === 'cash')
                                >
                                    Tunai
                                </option>

                                <option
                                    value="bank_transfer"
                                    @selected($paymentMethod === 'bank_transfer')
                                >
                                    Transfer Bank
                                </option>

                                <option
                                    value="online"
                                    @selected($paymentMethod === 'online')
                                >
                                    Online
                                </option>

                            </select>

                        </div>


                        {{-- =================================================
                        TANGGAL MULAI
                        ================================================== --}}

                        <div class="col-md-6">

                            <label
                                for="filter_date_from"
                                class="form-label"
                            >
                                Tanggal Mulai
                            </label>

                            <input
                                type="date"
                                name="date_from"
                                id="filter_date_from"
                                class="form-control"
                                value="{{ $dateFrom }}"
                            >

                        </div>


                        {{-- =================================================
                        TANGGAL SAMPAI
                        ================================================== --}}

                        <div class="col-md-6">

                            <label
                                for="filter_date_to"
                                class="form-label"
                            >
                                Tanggal Sampai
                            </label>

                            <input
                                type="date"
                                name="date_to"
                                id="filter_date_to"
                                class="form-control"
                                value="{{ $dateTo }}"
                            >

                        </div>


                        {{-- =================================================
                        NOMOR / REFERENSI
                        ================================================== --}}

                        <div class="col-12">

                            <label
                                for="filter_payment_search"
                                class="form-label"
                            >
                                Nomor Pembayaran / Referensi
                            </label>

                            <input
                                type="text"
                                name="search"
                                id="filter_payment_search"
                                class="form-control"
                                value="{{ $search }}"
                                maxlength="100"
                                placeholder="Nomor pembayaran atau referensi transaksi..."
                            >

                        </div>

                    </div>

                </div>


                {{-- =====================================================
                FOOTER
                ====================================================== --}}

                <div class="modal-footer">

                    <a
                        href="{{ route('admin.finance.payments.index') }}"
                        class="btn btn-light border"
                    >
                        <i class="bi bi-arrow-counterclockwise me-1"></i>
                        Reset
                    </a>

                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal"
                    >
                        Batal
                    </button>

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        <i class="bi bi-funnel me-1"></i>
                        Terapkan
                    </button>

                </div>

            </form>

        </div>

    </div>
</div>
