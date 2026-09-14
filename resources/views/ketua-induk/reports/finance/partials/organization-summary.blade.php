<div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">

    <div class="border-b border-gray-200 px-5 py-4 sm:px-6">

        <h3 class="text-base font-semibold text-gray-900">
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

                    <tr class="transition hover:bg-gray-50">

                        <td class="px-6 py-4">

                            <div class="font-medium text-gray-900">
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

                        <td class="whitespace-nowrap px-6 py-4 text-right text-gray-700">
                            Rp {{ number_format($row->total_income, 0, ',', '.') }}
                        </td>

                        <td class="whitespace-nowrap px-6 py-4 text-right text-gray-700">
                            Rp {{ number_format($row->total_expense, 0, ',', '.') }}
                        </td>

                        <td class="whitespace-nowrap px-6 py-4 text-right text-gray-700">
                            Rp {{ number_format($row->total_deposit, 0, ',', '.') }}
                        </td>

                        <td class="whitespace-nowrap px-6 py-4 text-right font-semibold text-gray-900">
                            Rp {{ number_format($row->net_balance, 0, ',', '.') }}
                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="5"
                            class="px-6 py-12 text-center">

                            <div class="flex flex-col items-center justify-center">

                                <div class="flex h-12 w-12 items-center justify-center
                                            rounded-full bg-gray-100">

                                    <i class="bi bi-bar-chart text-xl text-gray-400"></i>

                                </div>

                                <p class="mt-3 text-sm font-medium text-gray-700">
                                    Tidak ada data keuangan
                                </p>

                                <p class="mt-1 text-sm text-gray-500">
                                    Tidak ada data keuangan pada periode dan filter yang dipilih.
                                </p>

                            </div>

                        </td>

                    </tr>

                @endforelse

            </tbody>

            @if ($organizationSummary->isNotEmpty())

                <tfoot class="border-t-2 border-gray-200 bg-gray-50">

                    <tr>

                        <th class="px-6 py-4 text-left font-semibold text-gray-900">
                            Total
                        </th>

                        <th class="px-6 py-4 text-right font-semibold text-gray-900">
                            Rp {{ number_format($organizationSummary->sum('total_income'), 0, ',', '.') }}
                        </th>

                        <th class="px-6 py-4 text-right font-semibold text-gray-900">
                            Rp {{ number_format($organizationSummary->sum('total_expense'), 0, ',', '.') }}
                        </th>

                        <th class="px-6 py-4 text-right font-semibold text-gray-900">
                            Rp {{ number_format($organizationSummary->sum('total_deposit'), 0, ',', '.') }}
                        </th>

                        <th class="px-6 py-4 text-right font-semibold text-gray-900">
                            Rp {{ number_format($organizationSummary->sum('net_balance'), 0, ',', '.') }}
                        </th>

                    </tr>

                </tfoot>

            @endif

        </table>

    </div>

</div>
