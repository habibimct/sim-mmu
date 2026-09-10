<div class="card card-outline card-primary">

    <div class="card-header">

        <h3 class="card-title">
            <i class="fas fa-building mr-1"></i>
            Rekap Keuangan per Unit
        </h3>

    </div>

    <div class="card-body p-0">

        <div class="table-responsive">

            <table class="table table-bordered table-hover mb-0">

                <thead class="thead-light">

                    <tr>

                        <th width="60">
                            #
                        </th>

                        <th>
                            Unit
                        </th>

                        <th class="text-right">
                            Pemasukan
                        </th>

                        <th class="text-right">
                            Pengeluaran
                        </th>

                        <th class="text-right">
                            Saldo
                        </th>

                    </tr>

                </thead>

                <tbody>

                    @forelse (
                        $organizationSummary
                        as $index => $row
                    )

                        <tr>

                            <td>
                                {{ $index + 1 }}
                            </td>

                            <td>
                                {{ $row->organization?->name ?? '-' }}
                            </td>

                            <td class="text-right text-success">

                                Rp
                                {{ number_format(
                                    $row->total_income,
                                    0,
                                    ',',
                                    '.'
                                ) }}

                            </td>

                            <td class="text-right text-danger">

                                Rp
                                {{ number_format(
                                    $row->total_expense,
                                    0,
                                    ',',
                                    '.'
                                ) }}

                            </td>

                            <td class="text-right font-weight-bold">

                                Rp
                                {{ number_format(
                                    $row->net_balance,
                                    0,
                                    ',',
                                    '.'
                                ) }}

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="5"
                                class="text-center text-muted py-4"
                            >
                                Tidak ada data keuangan
                                pada periode yang dipilih.
                            </td>

                        </tr>

                    @endforelse

                </tbody>


                @if ($organizationSummary->count())

                    <tfoot class="font-weight-bold">

                        <tr>

                            <td colspan="2">
                                TOTAL
                            </td>

                            <td class="text-right text-success">

                                Rp
                                {{ number_format(
                                    $totalIncome,
                                    0,
                                    ',',
                                    '.'
                                ) }}

                            </td>

                            <td class="text-right text-danger">

                                Rp
                                {{ number_format(
                                    $totalExpense,
                                    0,
                                    ',',
                                    '.'
                                ) }}

                            </td>

                            <td class="text-right">

                                Rp
                                {{ number_format(
                                    $netBalance,
                                    0,
                                    ',',
                                    '.'
                                ) }}

                            </td>

                        </tr>

                    </tfoot>

                @endif

            </table>

        </div>

    </div>

</div>
