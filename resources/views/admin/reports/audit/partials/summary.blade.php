<div class="row g-3 mb-4">

    {{-- Total Aktivitas --}}
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">

                <div class="d-flex align-items-center">

                    <div
                        class="rounded-circle bg-primary bg-opacity-10 text-primary
                               d-flex align-items-center justify-content-center flex-shrink-0"
                        style="width:52px;height:52px;"
                    >
                        <i class="bi bi-shield-check fs-4"></i>
                    </div>

                    <div class="ms-3">
                        <div class="text-muted small">
                            Total Aktivitas
                        </div>

                        <div class="fs-4 fw-bold">
                            {{ number_format($totalActivities) }}
                        </div>
                    </div>

                </div>

                <div class="border-top mt-3 pt-3">
                    <small class="text-muted">
                        Seluruh aktivitas audit
                    </small>
                </div>

            </div>
        </div>
    </div>


    {{-- Dibuat --}}
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">

                <div class="d-flex align-items-center">

                    <div
                        class="rounded-circle bg-success bg-opacity-10 text-success
                               d-flex align-items-center justify-content-center flex-shrink-0"
                        style="width:52px;height:52px;"
                    >
                        <i class="bi bi-plus-circle fs-4"></i>
                    </div>

                    <div class="ms-3">
                        <div class="text-muted small">
                            Dibuat
                        </div>

                        <div class="fs-4 fw-bold">
                            {{ number_format($createdActivities) }}
                        </div>
                    </div>

                </div>

                <div class="border-top mt-3 pt-3">
                    <small class="text-muted">
                        Data yang dibuat
                    </small>
                </div>

            </div>
        </div>
    </div>


    {{-- Diperbarui --}}
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">

                <div class="d-flex align-items-center">

                    <div
                        class="rounded-circle bg-warning bg-opacity-10 text-warning
                               d-flex align-items-center justify-content-center flex-shrink-0"
                        style="width:52px;height:52px;"
                    >
                        <i class="bi bi-pencil-square fs-4"></i>
                    </div>

                    <div class="ms-3">
                        <div class="text-muted small">
                            Diperbarui
                        </div>

                        <div class="fs-4 fw-bold">
                            {{ number_format($updatedActivities) }}
                        </div>
                    </div>

                </div>

                <div class="border-top mt-3 pt-3">
                    <small class="text-muted">
                        Data yang diperbarui
                    </small>
                </div>

            </div>
        </div>
    </div>


    {{-- Dihapus --}}
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">

                <div class="d-flex align-items-center">

                    <div
                        class="rounded-circle bg-danger bg-opacity-10 text-danger
                               d-flex align-items-center justify-content-center flex-shrink-0"
                        style="width:52px;height:52px;"
                    >
                        <i class="bi bi-trash fs-4"></i>
                    </div>

                    <div class="ms-3">
                        <div class="text-muted small">
                            Dihapus
                        </div>

                        <div class="fs-4 fw-bold">
                            {{ number_format($deletedActivities) }}
                        </div>
                    </div>

                </div>

                <div class="border-top mt-3 pt-3">
                    <small class="text-muted">
                        Data yang dihapus
                    </small>
                </div>

            </div>
        </div>
    </div>

</div>
