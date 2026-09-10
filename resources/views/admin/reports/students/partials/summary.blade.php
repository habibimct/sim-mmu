{{-- ============================================================
     SUMMARY
     ============================================================ --}}

<div class="row g-2 mb-2 mt-2">

    {{-- Total Siswa --}}
    <div class="col-md-4">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-body d-flex align-items-center">

                <div class="flex-grow-1">
                    <div class="text-muted small mb-1">
                        Total Siswa
                    </div>

                    <div class="fs-3 fw-bold text-dark">
                        {{ number_format($totalStudents) }}
                    </div>
                </div>

                <div class="ms-3">
                    <div class="rounded-circle bg-primary-subtle
                                text-primary d-flex align-items-center
                                justify-content-center"
                         style="width: 52px; height: 52px;">

                        <i class="bi bi-people fs-4"></i>

                    </div>
                </div>

            </div>
        </div>
    </div>


    {{-- Laki-laki --}}
    <div class="col-md-4">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-body d-flex align-items-center">

                <div class="flex-grow-1">
                    <div class="text-muted small mb-1">
                        Laki-laki
                    </div>

                    <div class="fs-3 fw-bold text-dark">
                        {{ number_format($totalMale) }}
                    </div>
                </div>

                <div class="ms-3">
                    <div class="rounded-circle bg-info-subtle
                                text-info d-flex align-items-center
                                justify-content-center"
                         style="width: 52px; height: 52px;">

                        <i class="bi bi-gender-male fs-4"></i>

                    </div>
                </div>

            </div>
        </div>
    </div>


    {{-- Perempuan --}}
    <div class="col-md-4">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-body d-flex align-items-center">

                <div class="flex-grow-1">
                    <div class="text-muted small mb-1">
                        Perempuan
                    </div>

                    <div class="fs-3 fw-bold text-dark">
                        {{ number_format($totalFemale) }}
                    </div>
                </div>

                <div class="ms-3">
                    <div class="rounded-circle bg-danger-subtle
                                text-danger d-flex align-items-center
                                justify-content-center"
                         style="width: 52px; height: 52px;">

                        <i class="bi bi-gender-female fs-4"></i>

                    </div>
                </div>

            </div>
        </div>
    </div>

</div>


{{-- ============================================================
     REKAP PER UNIT
     ============================================================ --}}

<div class="card shadow-sm">

    <div class="card-header bg-white d-flex justify-content-between align-items-center">

        <div>
            <h3 class="card-title fw-semibold mb-0">
                <i class="bi bi-building me-1"></i>
                Rekap Siswa per Unit
            </h3>
        </div>

        <span class="badge bg-secondary">
            {{ number_format($organizationSummary->count()) }} Unit
        </span>

    </div>


    <div class="card-body p-0">

        <div class="table-responsive">

            <table class="table table-hover align-middle mb-0">

                <thead class="table-light">

                    <tr>

                        <th
                            width="60"
                            class="text-center"
                        >
                            No
                        </th>

                        <th>
                            Unit
                        </th>

                        <th
                            width="150"
                            class="text-center"
                        >
                            Laki-laki
                        </th>

                        <th
                            width="150"
                            class="text-center"
                        >
                            Perempuan
                        </th>

                        <th
                            width="130"
                            class="text-center"
                        >
                            Total
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @forelse ($organizationSummary as $index => $summary)

                        <tr>

                            <td class="text-center text-muted">
                                {{ $index + 1 }}
                            </td>

                            <td>
                                <span class="fw-semibold">
                                    {{ $summary['organization']->name ?? '-' }}
                                </span>
                            </td>

                            <td class="text-center">
                                {{ number_format($summary['male']) }}
                            </td>

                            <td class="text-center">
                                {{ number_format($summary['female']) }}
                            </td>

                            <td class="text-center">
                                <span class="fw-bold">
                                    {{ number_format($summary['total']) }}
                                </span>
                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="5"
                                class="text-center text-muted py-4"
                            >
                                <i class="bi bi-inbox fs-4 d-block mb-2"></i>

                                Belum ada data siswa.

                            </td>

                        </tr>

                    @endforelse

                </tbody>


                {{-- Total --}}
                @if ($organizationSummary->isNotEmpty())

                    <tfoot class="table-light">

                        <tr>

                            <th
                                colspan="2"
                                class="text-end"
                            >
                                Total
                            </th>

                            <th class="text-center">
                                {{ number_format($totalMale) }}
                            </th>

                            <th class="text-center">
                                {{ number_format($totalFemale) }}
                            </th>

                            <th class="text-center">
                                {{ number_format($totalStudents) }}
                            </th>

                        </tr>

                    </tfoot>

                @endif

            </table>

        </div>

    </div>

</div>
