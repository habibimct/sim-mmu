<div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200">

    <div class="border-b border-gray-200 px-6 py-4">
        <h3 class="text-base font-semibold text-gray-800">
            Rekap Keuangan per Unit
        </h3>

        <p class="mt-1 text-sm text-gray-500">
            Ringkasan transaksi berdasarkan organisasi dalam periode yang dipilih.
        </p>
    </div>

    <div class="overflow-x-auto">

        <table class="min-w-full divide-y divide-gray-200 text-sm">

            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left font-semibold text-gray-600">
                        Organisasi
                    </th>

                    <th class="px-6 py-3 text-right font-semibold text-gray-600">
                        Pemasukan
                    </th>

                    <th class="px-6 py-3 text-right font-semibold text-gray-600">
                        Pengeluaran
                    </th>

                    <th class="px-6 py-3 text-right font-semibold text-gray-600">
                        Setoran
                    </th>

                    <th class="px-6 py-3 text-right font-semibold text-gray-600">
                        Saldo
                    </th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100 bg-white">

                @forelse ($organizationSummary as $row)

                    <tr class="hover:bg-gray-50">

                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-800">
                                {{ $row->organization?->type === 'induk'
                                    ? 'PMUB'
                                    : ($row->organization?->name ?? '-') }}
                            </div>

                            @if ($row->organization?->type === 'unit')
                                <div class="mt-0.5 text-xs text-gray-400">
                                    Unit
                                </div>
                            @endif
                        </td>

                        <td class="whitespace-nowrap px-6 py-4 text-right">
                            Rp {{ number_format($row->total_income, 0, ',', '.') }}
                        </td>

                        <td class="whitespace-nowrap px-6 py-4 text-right">
                            Rp {{ number_format($row->total_expense, 0, ',', '.') }}
                        </td>

                        <td class="whitespace-nowrap px-6 py-4 text-right">
                            Rp {{ number_format($row->total_deposit, 0, ',', '.') }}
                        </td>

                        <td class="whitespace-nowrap px-6 py-4 text-right font-semibold">
                            Rp {{ number_format($row->net_balance, 0, ',', '.') }}
                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-sm text-gray-500">
                            Tidak ada data keuangan pada periode dan filter yang dipilih.
                        </td>
                    </tr>

                @endforelse

            </tbody>

            @if ($organizationSummary->isNotEmpty())

                <tfoot class="border-t-2 border-gray-200 bg-gray-50">

                    <tr>
                        <th class="px-6 py-4 text-left font-semibold text-gray-800">
                            Total
                        </th>

                        <th class="px-6 py-4 text-right font-semibold text-gray-800">
                            Rp {{ number_format($organizationSummary->sum('total_income'), 0, ',', '.') }}
                        </th>

                        <th class="px-6 py-4 text-right font-semibold text-gray-800">
                            Rp {{ number_format($organizationSummary->sum('total_expense'), 0, ',', '.') }}
                        </th>

                        <th class="px-6 py-4 text-right font-semibold text-gray-800">
                            Rp {{ number_format($organizationSummary->sum('total_deposit'), 0, ',', '.') }}
                        </th>

                        <th class="px-6 py-4 text-right font-semibold text-gray-800">
                            Rp {{ number_format($organizationSummary->sum('net_balance'), 0, ',', '.') }}
                        </th>
                    </tr>

                </tfoot>

            @endif

        </table>

    </div>

</div>
