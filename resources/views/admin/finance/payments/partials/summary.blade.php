{{-- =========================================================
RINGKASAN PEMBAYARAN
========================================================= --}}

<div class="row g-3 mb-4">

    {{-- =====================================================
    TOTAL
    ====================================================== --}}

    <div class="col-xl col-md-6">

        <div class="card h-100 border-0 shadow-sm">

            <div class="card-body">

                <div class="d-flex justify-content-between align-items-start">

                    <div>

                        <div class="text-muted small">
                            Total Pembayaran
                        </div>

                        <div class="fs-4 fw-bold">
                            {{ number_format($totalPayments, 0, ',', '.') }}
                        </div>

                        <div class="text-muted small mt-1">
                            Rp {{ number_format($totalAmount, 0, ',', '.') }}
                        </div>

                    </div>

                    <div class="text-primary fs-2">
                        <i class="bi bi-wallet2"></i>
                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- =====================================================
    MENUNGGU
    ====================================================== --}}

    <div class="col-xl col-md-6">

        <div class="card h-100 border-0 shadow-sm">

            <div class="card-body">

                <div class="d-flex justify-content-between align-items-start">

                    <div>

                        <div class="text-muted small">
                            Menunggu
                        </div>

                        <div class="fs-4 fw-bold">
                            {{ number_format($pendingPayments, 0, ',', '.') }}
                        </div>

                        <div class="text-muted small mt-1">
                            Rp {{ number_format($pendingAmount, 0, ',', '.') }}
                        </div>

                    </div>

                    <div class="text-warning fs-2">
                        <i class="bi bi-hourglass-split"></i>
                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- =====================================================
    DIKONFIRMASI
    ====================================================== --}}

    <div class="col-xl col-md-6">

        <div class="card h-100 border-0 shadow-sm">

            <div class="card-body">

                <div class="d-flex justify-content-between align-items-start">

                    <div>

                        <div class="text-muted small">
                            Dikonfirmasi
                        </div>

                        <div class="fs-4 fw-bold">
                            {{ number_format($confirmedPayments, 0, ',', '.') }}
                        </div>

                        <div class="text-muted small mt-1">
                            Rp {{ number_format($confirmedAmount, 0, ',', '.') }}
                        </div>

                    </div>

                    <div class="text-success fs-2">
                        <i class="bi bi-check-circle"></i>
                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- =====================================================
    GAGAL
    ====================================================== --}}

    <div class="col-xl col-md-6">

        <div class="card h-100 border-0 shadow-sm">

            <div class="card-body">

                <div class="d-flex justify-content-between align-items-start">

                    <div>

                        <div class="text-muted small">
                            Gagal
                        </div>

                        <div class="fs-4 fw-bold">
                            {{ number_format($failedPayments, 0, ',', '.') }}
                        </div>

                        <div class="text-muted small mt-1">
                            Rp {{ number_format($failedAmount, 0, ',', '.') }}
                        </div>

                    </div>

                    <div class="text-danger fs-2">
                        <i class="bi bi-x-circle"></i>
                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- =====================================================
    DIBATALKAN
    ====================================================== --}}

    <div class="col-xl col-md-6">

        <div class="card h-100 border-0 shadow-sm">

            <div class="card-body">

                <div class="d-flex justify-content-between align-items-start">

                    <div>

                        <div class="text-muted small">
                            Dibatalkan
                        </div>

                        <div class="fs-4 fw-bold">
                            {{ number_format($cancelledPayments, 0, ',', '.') }}
                        </div>

                        <div class="text-muted small mt-1">
                            Rp {{ number_format($cancelledAmount, 0, ',', '.') }}
                        </div>

                    </div>

                    <div class="text-secondary fs-2">
                        <i class="bi bi-slash-circle"></i>
                    </div>

                </div>

            </div>

        </div>

    </div>

</div>
