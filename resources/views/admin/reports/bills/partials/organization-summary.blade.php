<div class="card border-0 shadow-sm mb-4">

    <div class="card-header bg-white border-bottom py-3">

        <div class="d-flex align-items-center">

            <div class="bg-primary bg-opacity-10 rounded-3 p-2 me-3">
                <i class="bi bi-building text-primary fs-5"></i>
            </div>

            <div>
                <h5 class="mb-0 fw-semibold">
                    Rekap Tagihan per Unit
                </h5>

                <small class="text-muted">
                    Ringkasan tagihan berdasarkan unit
                </small>
            </div>

        </div>

    </div>


    <div class="card-body p-0">

        <div class="table-responsive">

            <table class="table table-hover align-middle mb-0">

                <thead class="table-light">

                    <tr>

                        <th class="text-center">
                            No
                        </th>

                        <th>
                            Unit
                        </th>

                        <th class="text-center">
                            Jumlah Tagihan
                        </th>

                        <th class="text-end">
                            Nilai Tagihan
                        </th>

                        <th class="text-end">
                            Sudah Dibayar
                        </th>

                        <th class="text-end">
                            Sisa Tagihan
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @forelse ($organizationSummary as $item)

                        <tr>

                            <td class="text-center">
                                {{ $loop->iteration }}
                            </td>


                            <td>

                                <div class="fw-semibold">
                                    {{ $item->organization?->name ?? '-' }}
                                </div>

                                @if ($item->organization?->code)

                                    <small class="text-muted">
                                        {{ $item->organization->code }}
                                    </small>

                                @endif

                            </td>


                            <td class="text-center">

                                <span class="badge bg-light text-dark border">
                                    {{ number_format($item->total_bills, 0, ',', '.') }}
                                </span>

                            </td>


                            <td class="text-end fw-semibold">

                                Rp
                                {{ number_format($item->total_amount, 0, ',', '.') }}

                            </td>


                            <td class="text-end text-success fw-semibold">

                                Rp
                                {{ number_format($item->total_paid, 0, ',', '.') }}

                            </td>


                            <td class="text-end text-danger fw-semibold">

                                Rp
                                {{ number_format($item->total_remaining, 0, ',', '.') }}

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="6"
                                class="text-center py-5 text-muted"
                            >

                                <i class="bi bi-building fs-1 d-block mb-2"></i>

                                Belum ada data rekap unit.

                            </td>

                        </tr>

                    @endforelse

                </tbody>


                @if ($organizationSummary->isNotEmpty())

                    <tfoot class="table-light">

                        <tr>

                            <th colspan="2" class="text-end">
                                Total
                            </th>

                            <th class="text-center">
                                {{ number_format($organizationSummary->sum('total_bills'), 0, ',', '.') }}
                            </th>

                            <th class="text-end">
                                Rp
                                {{ number_format($organizationSummary->sum('total_amount'), 0, ',', '.') }}
                            </th>

                            <th class="text-end text-success">
                                Rp
                                {{ number_format($organizationSummary->sum('total_paid'), 0, ',', '.') }}
                            </th>

                            <th class="text-end text-danger">
                                Rp
                                {{ number_format($organizationSummary->sum('total_remaining'), 0, ',', '.') }}
                            </th>

                        </tr>

                    </tfoot>

                @endif

            </table>

        </div>

    </div>

</div>
