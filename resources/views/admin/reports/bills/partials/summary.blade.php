<div class="row g-3 mb-4">

    {{-- Total Tagihan --}}
    <div class="col-md-6 col-xl-3">

        <div class="card border-0 shadow-sm h-100">

            <div class="card-body p-4">

                <div class="d-flex justify-content-between align-items-start">

                    <div>

                        <p class="text-muted mb-2 fw-semibold">
                            Total Tagihan
                        </p>

                        <h3 class="fw-bold mb-1">
                            {{ number_format($totalBills, 0, ',', '.') }}
                        </h3>

                        <small class="text-muted">
                            tagihan aktif
                        </small>

                    </div>

                    <div class="bg-primary bg-opacity-10 rounded-3 p-3">

                        <i class="bi bi-receipt text-primary fs-3"></i>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- Total Nilai --}}
    <div class="col-md-6 col-xl-3">

        <div class="card border-0 shadow-sm h-100">

            <div class="card-body p-4">

                <div class="d-flex justify-content-between align-items-start">

                    <div>

                        <p class="text-muted mb-2 fw-semibold">
                            Nilai Tagihan
                        </p>

                        <h3 class="fw-bold mb-1">
                            Rp {{ number_format($totalBillAmount, 0, ',', '.') }}
                        </h3>

                        <small class="text-muted">
                            nilai tagihan aktif
                        </small>

                    </div>

                    <div class="bg-warning bg-opacity-10 rounded-3 p-3">

                        <i class="bi bi-cash-stack text-warning fs-3"></i>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- Sudah Dibayar --}}
    <div class="col-md-6 col-xl-3">

        <div class="card border-0 shadow-sm h-100">

            <div class="card-body p-4">

                <div class="d-flex justify-content-between align-items-start">

                    <div>

                        <p class="text-muted mb-2 fw-semibold">
                            Sudah Dibayar
                        </p>

                        <h3 class="fw-bold mb-1 text-success">
                            Rp {{ number_format($totalPaidAmount, 0, ',', '.') }}
                        </h3>

                        <small class="text-muted">
                            pembayaran terkonfirmasi
                        </small>

                    </div>

                    <div class="bg-success bg-opacity-10 rounded-3 p-3">

                        <i class="bi bi-check-circle-fill text-success fs-3"></i>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- Sisa --}}
    <div class="col-md-6 col-xl-3">

        <div class="card border-0 shadow-sm h-100">

            <div class="card-body p-4">

                <div class="d-flex justify-content-between align-items-start">

                    <div>

                        <p class="text-muted mb-2 fw-semibold">
                            Sisa Tagihan
                        </p>

                        <h3 class="fw-bold mb-1 text-danger">
                            Rp {{ number_format($totalRemainingAmount, 0, ',', '.') }}
                        </h3>

                        <small class="text-muted">
                            belum tertagih
                        </small>

                    </div>

                    <div class="bg-danger bg-opacity-10 rounded-3 p-3">

                        <i class="bi bi-hourglass-split text-danger fs-3"></i>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>


{{-- Status --}}
<div class="card border-0 shadow-sm mb-4">

    <div class="card-header bg-white border-bottom py-3">

        <h5 class="mb-0 fw-semibold">
            <i class="bi bi-pie-chart me-2 text-primary"></i>
            Status Tagihan
        </h5>

    </div>


    <div class="card-body">

        <div class="row g-3">

            <div class="col-md-3">

                <div class="border rounded-3 p-3">

                    <div class="d-flex justify-content-between">

                        <span class="text-muted">
                            Lunas
                        </span>

                        <span class="badge bg-success">
                            {{ number_format($paidBillCount, 0, ',', '.') }}
                        </span>

                    </div>

                </div>

            </div>


            <div class="col-md-3">

                <div class="border rounded-3 p-3">

                    <div class="d-flex justify-content-between">

                        <span class="text-muted">
                            Sebagian Dibayar
                        </span>

                        <span class="badge bg-warning text-dark">
                            {{ number_format($partialBillCount, 0, ',', '.') }}
                        </span>

                    </div>

                </div>

            </div>


            <div class="col-md-3">

                <div class="border rounded-3 p-3">

                    <div class="d-flex justify-content-between">

                        <span class="text-muted">
                            Belum Dibayar
                        </span>

                        <span class="badge bg-danger">
                            {{ number_format($unpaidBillCount, 0, ',', '.') }}
                        </span>

                    </div>

                </div>

            </div>


            <div class="col-md-3">

                <div class="border rounded-3 p-3">

                    <div class="d-flex justify-content-between">

                        <span class="text-muted">
                            Dibatalkan
                        </span>

                        <span class="badge bg-secondary">
                            {{ number_format($cancelledBillCount, 0, ',', '.') }}
                        </span>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>
