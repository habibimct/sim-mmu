
    <div class="row g-3 mb-2 mt-1">

        {{-- Total Pemasukan --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">

                    <div class="d-flex align-items-center justify-content-between">

                        <div>
                            <p class="text-muted small mb-1">
                                Total Pemasukan
                            </p>

                            <h4 class="fw-bold text-success mb-0">
                                Rp
                                {{ number_format($totalIncome, 0, ',', '.') }}
                            </h4>
                        </div>

                        <div class="rounded-circle bg-success-subtle
                                text-success p-3">
                            <i class="bi bi-arrow-down-circle fs-4"></i>
                        </div>

                    </div>

                </div>
            </div>
        </div>


        {{-- Total Pengeluaran --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">

                    <div class="d-flex align-items-center justify-content-between">

                        <div>
                            <p class="text-muted small mb-1">
                                Total Pengeluaran
                            </p>

                            <h4 class="fw-bold text-danger mb-0">
                                Rp
                                {{ number_format($totalExpense, 0, ',', '.') }}
                            </h4>
                        </div>

                        <div class="rounded-circle bg-danger-subtle
                                text-danger p-3">
                            <i class="bi bi-arrow-up-circle fs-4"></i>
                        </div>

                    </div>

                </div>
            </div>
        </div>


        {{-- Selisih Bersih --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">

                    <div class="d-flex align-items-center justify-content-between">

                        <div>
                            <p class="text-muted small mb-1">
                                Selisih Bersih
                            </p>

                            <h4
                                class="fw-bold mb-0
                            {{ $netBalance >= 0 ? 'text-primary' : 'text-danger' }}">

                                Rp
                                {{ number_format($netBalance, 0, ',', '.') }}

                            </h4>
                        </div>

                        <div class="rounded-circle bg-primary-subtle
                                text-primary p-3">
                            <i class="bi bi-wallet2 fs-4"></i>
                        </div>

                    </div>

                </div>
            </div>
        </div>

    </div>
