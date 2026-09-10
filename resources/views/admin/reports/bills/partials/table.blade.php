<div class="card border-0 shadow-sm">

    <div class="card-header bg-white border-bottom py-3">

        <div class="d-flex justify-content-between align-items-center">

            <div>

                <h5 class="mb-0 fw-semibold">
                    <i class="bi bi-table me-2 text-primary"></i>
                    Detail Tagihan
                </h5>

                <small class="text-muted">
                    Daftar tagihan berdasarkan filter yang dipilih
                </small>

            </div>

            <span class="badge bg-light text-dark border">
                {{ number_format($bills->total(), 0, ',', '.') }}
                data
            </span>

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
                            Siswa
                        </th>

                        <th>
                            Unit
                        </th>

                        <th>
                            Jenis Tagihan
                        </th>

                        <th>
                            Periode
                        </th>

                        <th class="text-end">
                            Nominal
                        </th>

                        <th>
                            Jatuh Tempo
                        </th>

                        <th class="text-center">
                            Status
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @forelse ($bills as $bill)

                        <tr>

                            <td class="text-center">

                                {{ $bills->firstItem() + $loop->index }}

                            </td>


                            <td>

                                <div class="fw-semibold">

                                    {{
                                        $bill->studentAcademicYear?->student?->name
                                        ?? '-'
                                    }}

                                </div>

                                <small class="text-muted">

                                    NIS:
                                    {{
                                        $bill->studentAcademicYear?->student?->nis
                                        ?? '-'
                                    }}

                                </small>

                            </td>


                            <td>

                                {{
                                    $bill->studentAcademicYear?->organization?->name
                                    ?? '-'
                                }}

                            </td>


                            <td>

                                <div class="fw-semibold">

                                    {{ $bill->billType?->name ?? '-' }}

                                </div>

                                @if ($bill->billType?->code)

                                    <small class="text-muted">
                                        {{ $bill->billType->code }}
                                    </small>

                                @endif

                            </td>


                            <td>

                                {{ $bill->period ?? '-' }}

                            </td>


                            <td class="text-end fw-semibold">

                                Rp {{ number_format($bill->amount, 0, ',', '.') }}

                            </td>


                            <td>

                                {{
                                    $bill->due_date
                                        ? $bill->due_date->format('d/m/Y')
                                        : '-'
                                }}

                            </td>


                            <td class="text-center">

                                @if ($bill->status === 'cancelled')

                                    <span class="badge bg-secondary">
                                        Dibatalkan
                                    </span>

                                @elseif ($bill->status === 'active')

                                    <span class="badge bg-success">
                                        Aktif
                                    </span>

                                @else

                                    <span class="badge bg-light text-dark border">
                                        {{ ucfirst($bill->status) }}
                                    </span>

                                @endif

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="8"
                                class="text-center py-5"
                            >

                                <div class="text-muted">

                                    <i class="bi bi-receipt fs-1 d-block mb-2"></i>

                                    Belum ada data tagihan.

                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>


    @if ($bills->hasPages())
        <div class="d-flex justify-content-end align-items-center p-3 border-top">

            <div>
            {{ $bills->onEachSide(1)->links('pagination::bootstrap-5') }}
            </div>

        </div>
    @endif

</div>
