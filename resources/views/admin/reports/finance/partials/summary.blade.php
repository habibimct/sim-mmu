<div class="row g-3 mb-4">

    {{-- Total Pemasukan --}}
    <div class="col-md-4">

        <div class="card border-0 shadow-sm h-100">

            <div class="card-body p-4">

                <div class="d-flex justify-content-between align-items-start">

                    <div>

                        <p class="text-muted mb-2 fw-semibold">
                            Total Pemasukan
                        </p>

                        <h3 class="fw-bold mb-0 text-success">
                            Rp {{ number_format($totalIncome, 0, ',', '.') }}
                        </h3>

                    </div>

                    <div class="bg-success bg-opacity-10 rounded-3 p-3">

                        <i class="bi bi-arrow-down-circle-fill text-success fs-3"></i>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- Total Pengeluaran --}}
    <div class="col-md-4">

        <div class="card border-0 shadow-sm h-100">

            <div class="card-body p-4">

                <div class="d-flex justify-content-between align-items-start">

                    <div>

                        <p class="text-muted mb-2 fw-semibold">
                            Total Pengeluaran
                        </p>

                        <h3 class="fw-bold mb-0 text-danger">
                            Rp {{ number_format($totalExpense, 0, ',', '.') }}
                        </h3>

                    </div>

                    <div class="bg-danger bg-opacity-10 rounded-3 p-3">

                        <i class="bi bi-arrow-up-circle-fill text-danger fs-3"></i>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- Saldo Bersih --}}
    <div class="col-md-4">

        <div class="card border-0 shadow-sm h-100">

            <div class="card-body p-4">

                <div class="d-flex justify-content-between align-items-start">

                    <div>

                        <p class="text-muted mb-2 fw-semibold">
                            Saldo Bersih
                        </p>

                        <h3 class="fw-bold mb-0 text-info">
                            Rp {{ number_format($netBalance, 0, ',', '.') }}
                        </h3>

                    </div>

                    <div class="bg-info bg-opacity-10 rounded-3 p-3">

                        <i class="bi bi-wallet2 text-info fs-3"></i>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>
