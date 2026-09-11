<div class="row g-3 mb-4">

    {{-- TOTAL --}}
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">

                <div class="d-flex align-items-center">

                    <div class="rounded-circle bg-primary bg-opacity-10
                                text-primary d-flex align-items-center
                                justify-content-center flex-shrink-0"
                         style="width: 52px; height: 52px;">

                        <i class="bi bi-credit-card fs-4"></i>

                    </div>

                    <div class="ms-3">

                        <div class="text-muted small">
                            Total Pembayaran
                        </div>

                        <div class="fs-4 fw-bold">
                            {{ number_format(
                                $totalPayments,
                                0,
                                ',',
                                '.'
                            ) }}
                        </div>

                    </div>

                </div>

                <div class="border-top mt-3 pt-3">

                    <span class="text-muted small">
                        Total nominal
                    </span>

                    <div class="fw-semibold text-primary">
                        Rp
                        {{ number_format(
                            $totalAmount,
                            0,
                            ',',
                            '.'
                        ) }}
                    </div>

                </div>

            </div>
        </div>
    </div>


    {{-- CONFIRMED --}}
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">

                <div class="d-flex align-items-center">

                    <div class="rounded-circle bg-success bg-opacity-10
                                text-success d-flex align-items-center
                                justify-content-center flex-shrink-0"
                         style="width: 52px; height: 52px;">

                        <i class="bi bi-check-circle fs-4"></i>

                    </div>

                    <div class="ms-3">

                        <div class="text-muted small">
                            Dikonfirmasi
                        </div>

                        <div class="fs-4 fw-bold">
                            {{ number_format(
                                $confirmedPayments,
                                0,
                                ',',
                                '.'
                            ) }}
                        </div>

                    </div>

                </div>

                <div class="border-top mt-3 pt-3">

                    <span class="text-muted small">
                        Total nominal
                    </span>

                    <div class="fw-semibold text-success">
                        Rp
                        {{ number_format(
                            $confirmedAmount,
                            0,
                            ',',
                            '.'
                        ) }}
                    </div>

                </div>

            </div>
        </div>
    </div>


    {{-- PENDING --}}
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">

                <div class="d-flex align-items-center">

                    <div class="rounded-circle bg-warning bg-opacity-10
                                text-warning d-flex align-items-center
                                justify-content-center flex-shrink-0"
                         style="width: 52px; height: 52px;">

                        <i class="bi bi-hourglass-split fs-4"></i>

                    </div>

                    <div class="ms-3">

                        <div class="text-muted small">
                            Menunggu Konfirmasi
                        </div>

                        <div class="fs-4 fw-bold">
                            {{ number_format(
                                $pendingPayments,
                                0,
                                ',',
                                '.'
                            ) }}
                        </div>

                    </div>

                </div>

                <div class="border-top mt-3 pt-3">

                    <span class="text-muted small">
                        Total nominal
                    </span>

                    <div class="fw-semibold text-warning">
                        Rp
                        {{ number_format(
                            $pendingAmount,
                            0,
                            ',',
                            '.'
                        ) }}
                    </div>

                </div>

            </div>
        </div>
    </div>


    {{-- CANCELLED --}}
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">

                <div class="d-flex align-items-center">

                    <div class="rounded-circle bg-secondary bg-opacity-10
                                text-secondary d-flex align-items-center
                                justify-content-center flex-shrink-0"
                         style="width: 52px; height: 52px;">

                        <i class="bi bi-x-circle fs-4"></i>

                    </div>

                    <div class="ms-3">

                        <div class="text-muted small">
                            Dibatalkan
                        </div>

                        <div class="fs-4 fw-bold">
                            {{ number_format(
                                $cancelledPayments,
                                0,
                                ',',
                                '.'
                            ) }}
                        </div>

                    </div>

                </div>

                <div class="border-top mt-3 pt-3">

                    <span class="text-muted small">
                        Total nominal
                    </span>

                    <div class="fw-semibold text-secondary">
                        Rp
                        {{ number_format(
                            $cancelledAmount,
                            0,
                            ',',
                            '.'
                        ) }}
                    </div>

                </div>

            </div>
        </div>
    </div>

</div>
