{{-- SUMMARY ABSENSI --}}

<div class="row g-3 mb-3">

    {{-- TOTAL --}}

    <div class="col-md-4 col-lg">

        <div class="card h-100 shadow-sm border-0">

            <div class="card-body d-flex align-items-center">

                <div class="flex-grow-1">

                    <div class="text-muted small mb-1">
                        Total Kehadiran
                    </div>

                    <div class="fs-3 fw-bold text-dark">
                        {{ number_format($totalAttendance) }}
                    </div>

                </div>

                <div class="ms-3">

                    <div
                        class="rounded-circle bg-primary-subtle
                               text-primary d-flex align-items-center
                               justify-content-center"
                        style="width: 52px; height: 52px;"
                    >

                        <i class="bi bi-clipboard-data fs-4"></i>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- HADIR --}}

    <div class="col-md-4 col-lg">

        <div class="card h-100 shadow-sm border-0">

            <div class="card-body d-flex align-items-center">

                <div class="flex-grow-1">

                    <div class="text-muted small mb-1">
                        Hadir
                    </div>

                    <div class="fs-3 fw-bold text-dark">
                        {{ number_format($totalPresent) }}
                    </div>

                </div>

                <div class="ms-3">

                    <div
                        class="rounded-circle bg-success-subtle
                               text-success d-flex align-items-center
                               justify-content-center"
                        style="width: 52px; height: 52px;"
                    >

                        <i class="bi bi-check-circle fs-4"></i>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- SAKIT --}}

    <div class="col-md-4 col-lg">

        <div class="card h-100 shadow-sm border-0">

            <div class="card-body d-flex align-items-center">

                <div class="flex-grow-1">

                    <div class="text-muted small mb-1">
                        Sakit
                    </div>

                    <div class="fs-3 fw-bold text-dark">
                        {{ number_format($totalSick) }}
                    </div>

                </div>

                <div class="ms-3">

                    <div
                        class="rounded-circle bg-warning-subtle
                               text-warning d-flex align-items-center
                               justify-content-center"
                        style="width: 52px; height: 52px;"
                    >

                        <i class="bi bi-bandaid fs-4"></i>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- IZIN --}}

    <div class="col-md-4 col-lg">

        <div class="card h-100 shadow-sm border-0">

            <div class="card-body d-flex align-items-center">

                <div class="flex-grow-1">

                    <div class="text-muted small mb-1">
                        Izin
                    </div>

                    <div class="fs-3 fw-bold text-dark">
                        {{ number_format($totalPermission) }}
                    </div>

                </div>

                <div class="ms-3">

                    <div
                        class="rounded-circle bg-info-subtle
                               text-info d-flex align-items-center
                               justify-content-center"
                        style="width: 52px; height: 52px;"
                    >

                        <i class="bi bi-envelope-check fs-4"></i>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- ALPA --}}

    <div class="col-md-4 col-lg">

        <div class="card h-100 shadow-sm border-0">

            <div class="card-body d-flex align-items-center">

                <div class="flex-grow-1">

                    <div class="text-muted small mb-1">
                        Alpa
                    </div>

                    <div class="fs-3 fw-bold text-dark">
                        {{ number_format($totalAbsent) }}
                    </div>

                </div>

                <div class="ms-3">

                    <div
                        class="rounded-circle bg-danger-subtle
                               text-danger d-flex align-items-center
                               justify-content-center"
                        style="width: 52px; height: 52px;"
                    >

                        <i class="bi bi-x-circle fs-4"></i>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>
