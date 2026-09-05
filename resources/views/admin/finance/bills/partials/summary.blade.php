{{-- =========================================================
RINGKASAN TAGIHAN
========================================================= --}}

<div class="row g-3 mb-4">

    {{-- =====================================================
    TOTAL TAGIHAN
    ====================================================== --}}

    <div class="col-xl col-md-6">

        <div class="card h-100 border-0 shadow-sm">

            <div class="card-body">

                <div class="d-flex justify-content-between">

                    <div>

                        <div class="text-muted small">
                            Total Tagihan
                        </div>

                        <div class="fs-4 fw-bold">
                            {{ number_format($totalBills, 0, ',', '.') }}
                        </div>

                        <div class="text-muted small mt-1">
                            Rp {{ number_format($totalAmount, 0, ',', '.') }}
                        </div>

                    </div>

                    <div class="text-primary fs-2">
                        <i class="bi bi-receipt"></i>
                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- =====================================================
    BELUM BAYAR
    ====================================================== --}}

    <div class="col-xl col-md-6">

        <div class="card h-100 border-0 shadow-sm">

            <div class="card-body">

                <div class="d-flex justify-content-between">

                    <div>

                        <div class="text-muted small">
                            Belum Bayar
                        </div>

                        <div class="fs-4 fw-bold">
                            {{ number_format($unpaidBills, 0, ',', '.') }}
                        </div>

                        <div class="text-muted small mt-1">
                            Rp {{ number_format($unpaidAmount, 0, ',', '.') }}
                        </div>

                    </div>

                    <div class="text-danger fs-2">
                        <i class="bi bi-exclamation-circle"></i>
                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- =====================================================
    SEBAGIAN
    ====================================================== --}}

    <div class="col-xl col-md-6">

        <div class="card h-100 border-0 shadow-sm">

            <div class="card-body">

                <div class="d-flex justify-content-between">

                    <div>

                        <div class="text-muted small">
                            Sebagian
                        </div>

                        <div class="fs-4 fw-bold">
                            {{ number_format($partialBills, 0, ',', '.') }}
                        </div>

                        <div class="text-muted small mt-1">
                            Rp {{ number_format($partialAmount, 0, ',', '.') }}
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
    LUNAS
    ====================================================== --}}

    <div class="col-xl col-md-6">

        <div class="card h-100 border-0 shadow-sm">

            <div class="card-body">

                <div class="d-flex justify-content-between">

                    <div>

                        <div class="text-muted small">
                            Lunas
                        </div>

                        <div class="fs-4 fw-bold">
                            {{ number_format($paidBills, 0, ',', '.') }}
                        </div>

                        <div class="text-muted small mt-1">
                            Rp {{ number_format($paidAmount, 0, ',', '.') }}
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
    DIBATALKAN
    ====================================================== --}}

    <div class="col-xl col-md-6">

        <div class="card h-100 border-0 shadow-sm">

            <div class="card-body">

                <div class="d-flex justify-content-between">

                    <div>

                        <div class="text-muted small">
                            Dibatalkan
                        </div>

                        <div class="fs-4 fw-bold">
                            {{ number_format($cancelledBills, 0, ',', '.') }}
                        </div>

                        <div class="text-muted small mt-1">
                            Rp {{ number_format($cancelledAmount, 0, ',', '.') }}
                        </div>

                    </div>

                    <div class="text-secondary fs-2">
                        <i class="bi bi-x-circle"></i>
                    </div>

                </div>

            </div>

        </div>

    </div>

</div>
