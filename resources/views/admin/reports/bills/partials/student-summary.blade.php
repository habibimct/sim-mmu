<div class="card shadow-sm mb-4">
    <div class="card-header">
        <h3 class="card-title mb-0">
            <i class="fas fa-users mr-1"></i>
            Rekap Tagihan Tiap Siswa
        </h3>
    </div>

    <div class="card-body p-0">

        <div class="table-responsive">
            <table class="table table-bordered table-hover table-sm mb-0">

                <thead class="thead-light">
                    <tr>
                        <th style="width: 45px;">No</th>
                        <th>Siswa</th>
                        <th>NIS</th>
                        <th>Unit</th>
                        <th>Kelas</th>
                        <th class="text-center">Jml Tagihan</th>
                        <th class="text-right">Total Tagihan</th>
                        <th class="text-right">Sudah Dibayar</th>
                        <th class="text-right">Sisa Tagihan</th>
                        <th class="text-center">Lunas</th>
                        <th class="text-center">Sebagian</th>
                        <th class="text-center">Belum</th>
                        <th class="text-center">Batal</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse ($studentSummary as $index => $item)

                        @php
                            $student = $item['student'];
                            $organization = $item['organization'];
                            $schoolClass = $item['school_class'];
                        @endphp

                        <tr>

                            <td class="text-center">
                                {{ $index + 1 }}
                            </td>

                            <td>
                                <strong>
                                    {{ $student?->name ?? '-' }}
                                </strong>
                            </td>

                            <td>
                                {{ $student?->nis ?? '-' }}
                            </td>

                            <td>
                                {{ $organization?->name ?? '-' }}
                            </td>

                            <td>
                                {{ $schoolClass?->name ?? '-' }}
                            </td>

                            <td class="text-center">
                                {{ $item['bill_count'] }}
                            </td>

                            <td class="text-right">
                                Rp {{ number_format($item['total_bill_amount'], 0, ',', '.') }}
                            </td>

                            <td class="text-right text-success">
                                Rp {{ number_format($item['total_paid_amount'], 0, ',', '.') }}
                            </td>

                            <td class="text-right
                                {{ $item['total_remaining_amount'] > 0 ? 'text-danger font-weight-bold' : 'text-success' }}">

                                Rp {{ number_format($item['total_remaining_amount'], 0, ',', '.') }}

                            </td>

                            <td class="text-center">
                                {{ $item['paid_bill_count'] }}
                            </td>

                            <td class="text-center">
                                {{ $item['partial_bill_count'] }}
                            </td>

                            <td class="text-center">
                                {{ $item['unpaid_bill_count'] }}
                            </td>

                            <td class="text-center">
                                {{ $item['cancelled_bill_count'] }}
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="13" class="text-center text-muted py-4">
                                Tidak ada data tagihan siswa.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

                @if ($studentSummary->isNotEmpty())

                    <tfoot class="font-weight-bold">

                        @php
                            $grandTotalBill = $studentSummary->sum('total_bill_amount');
                            $grandTotalPaid = $studentSummary->sum('total_paid_amount');
                            $grandTotalRemaining = $studentSummary->sum('total_remaining_amount');

                            $grandBillCount = $studentSummary->sum('bill_count');
                            $grandPaidCount = $studentSummary->sum('paid_bill_count');
                            $grandPartialCount = $studentSummary->sum('partial_bill_count');
                            $grandUnpaidCount = $studentSummary->sum('unpaid_bill_count');
                            $grandCancelledCount = $studentSummary->sum('cancelled_bill_count');
                        @endphp

                        <tr>

                            <td colspan="5" class="text-right">
                                TOTAL
                            </td>

                            <td class="text-center">
                                {{ $grandBillCount }}
                            </td>

                            <td class="text-right">
                                Rp {{ number_format($grandTotalBill, 0, ',', '.') }}
                            </td>

                            <td class="text-right text-success">
                                Rp {{ number_format($grandTotalPaid, 0, ',', '.') }}
                            </td>

                            <td class="text-right text-danger">
                                Rp {{ number_format($grandTotalRemaining, 0, ',', '.') }}
                            </td>

                            <td class="text-center">
                                {{ $grandPaidCount }}
                            </td>

                            <td class="text-center">
                                {{ $grandPartialCount }}
                            </td>

                            <td class="text-center">
                                {{ $grandUnpaidCount }}
                            </td>

                            <td class="text-center">
                                {{ $grandCancelledCount }}
                            </td>

                        </tr>

                    </tfoot>

                @endif

            </table>
        </div>

    </div>
</div>
