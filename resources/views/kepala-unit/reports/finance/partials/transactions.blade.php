<div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200">

    <div class="border-b border-gray-200 px-6 py-4">

        <h3 class="text-base font-semibold text-gray-800">
            Detail Transaksi
        </h3>

        <p class="mt-1 text-sm text-gray-500">
            Daftar transaksi keuangan unit sesuai periode dan kategori yang dipilih.
        </p>

    </div>

    <div class="overflow-x-auto">

        <table class="min-w-full divide-y divide-gray-200 text-sm">

            <thead class="bg-gray-50">

                <tr>

                    <th class="whitespace-nowrap px-6 py-3 text-left font-semibold text-gray-600">
                        Tanggal
                    </th>

                    <th class="whitespace-nowrap px-6 py-3 text-left font-semibold text-gray-600">
                        Jenis
                    </th>

                    <th class="whitespace-nowrap px-6 py-3 text-left font-semibold text-gray-600">
                        Kategori
                    </th>

                    <th class="whitespace-nowrap px-6 py-3 text-right font-semibold text-gray-600">
                        Jumlah
                    </th>

                    <th class="whitespace-nowrap px-6 py-3 text-left font-semibold text-gray-600">
                        Metode
                    </th>

                    <th class="whitespace-nowrap px-6 py-3 text-left font-semibold text-gray-600">
                        Keterangan
                    </th>

                </tr>

            </thead>

            <tbody class="divide-y divide-gray-100 bg-white">

                @forelse ($transactions as $transaction)

                    @php
                        $isDeposit =
                            $transaction->source_type === 'deposit';
                    @endphp

                    <tr class="hover:bg-gray-50">

                        {{-- Tanggal --}}
                        <td class="whitespace-nowrap px-6 py-4 text-gray-700">

                            {{ $transaction->transaction_date?->format('d/m/Y') ?? '-' }}

                        </td>


                        {{-- Jenis --}}
                        <td class="whitespace-nowrap px-6 py-4">

                            @if ($isDeposit)

                                <span
                                    class="inline-flex rounded-full
                                           bg-purple-100 px-2.5 py-1
                                           text-xs font-medium text-purple-700">
                                    Setoran
                                </span>

                            @elseif ($transaction->type === 'income')

                                <span
                                    class="inline-flex rounded-full
                                           bg-green-100 px-2.5 py-1
                                           text-xs font-medium text-green-700">
                                    Pemasukan
                                </span>

                            @elseif ($transaction->type === 'expense')

                                <span
                                    class="inline-flex rounded-full
                                           bg-red-100 px-2.5 py-1
                                           text-xs font-medium text-red-700">
                                    Pengeluaran
                                </span>

                            @else

                                <span
                                    class="inline-flex rounded-full
                                           bg-gray-100 px-2.5 py-1
                                           text-xs font-medium text-gray-600">
                                    -
                                </span>

                            @endif

                        </td>


                        {{-- Kategori --}}
                        <td class="px-6 py-4 text-gray-700">

                            {{ $isDeposit
                                ? 'Setoran Unit'
                                : ($transaction->category ?? '-') }}

                        </td>


                        {{-- Jumlah --}}
                        <td class="whitespace-nowrap px-6 py-4 text-right font-medium text-gray-800">

                            Rp {{ number_format($transaction->amount, 0, ',', '.') }}

                        </td>


                        {{-- Metode --}}
                        <td class="whitespace-nowrap px-6 py-4 text-gray-700">

                            {{ $transaction->payment_method ?? '-' }}

                        </td>


                        {{-- Keterangan --}}
                        <td class="min-w-[250px] px-6 py-4 text-gray-700">

                            {{ $transaction->description ?? '-' }}

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="6"
                            class="px-6 py-10 text-center text-sm text-gray-500">

                            Tidak ada transaksi pada periode dan kategori yang dipilih.

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>


    {{-- Pagination --}}
    @if ($transactions->hasPages())

        <div class="border-t border-gray-200 px-6 py-4">

            {{ $transactions->links() }}

        </div>

    @endif

</div>
