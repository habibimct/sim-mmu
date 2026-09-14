<div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">

    {{-- Header --}}
    <div class="border-b border-gray-200 px-5 py-4 sm:px-6">

        <h3 class="text-base font-semibold text-gray-900">
            Detail Transaksi
        </h3>

        <p class="mt-1 text-sm text-gray-500">
            Daftar transaksi keuangan unit sesuai periode dan kategori yang dipilih.
        </p>

    </div>


    {{-- Table --}}
    <div class="overflow-x-auto">

        <table class="min-w-full text-sm">

            <thead class="bg-gray-50">

                <tr>

                    <th class="whitespace-nowrap px-6 py-3 text-left
                               text-xs font-semibold uppercase tracking-wider text-gray-500">
                        Tanggal
                    </th>

                    <th class="whitespace-nowrap px-6 py-3 text-left
                               text-xs font-semibold uppercase tracking-wider text-gray-500">
                        Jenis
                    </th>

                    <th class="whitespace-nowrap px-6 py-3 text-left
                               text-xs font-semibold uppercase tracking-wider text-gray-500">
                        Kategori
                    </th>

                    <th class="whitespace-nowrap px-6 py-3 text-right
                               text-xs font-semibold uppercase tracking-wider text-gray-500">
                        Jumlah
                    </th>

                    <th class="whitespace-nowrap px-6 py-3 text-left
                               text-xs font-semibold uppercase tracking-wider text-gray-500">
                        Metode
                    </th>

                    <th class="whitespace-nowrap px-6 py-3 text-left
                               text-xs font-semibold uppercase tracking-wider text-gray-500">
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

                    <tr class="transition hover:bg-gray-50">

                        {{-- Tanggal --}}
                        <td class="whitespace-nowrap px-6 py-4 text-gray-700">
                            {{ $transaction->transaction_date?->format('d/m/Y') ?? '-' }}
                        </td>


                        {{-- Jenis --}}
                        <td class="whitespace-nowrap px-6 py-4">

                            @if ($isDeposit)

                                <span
                                    class="inline-flex items-center rounded-full
                                           bg-purple-50 px-2.5 py-1
                                           text-xs font-semibold text-purple-700
                                           ring-1 ring-purple-100">
                                    Setoran
                                </span>

                            @elseif ($transaction->type === 'income')

                                <span
                                    class="inline-flex items-center rounded-full
                                           bg-green-50 px-2.5 py-1
                                           text-xs font-semibold text-green-700
                                           ring-1 ring-green-100">
                                    Pemasukan
                                </span>

                            @elseif ($transaction->type === 'expense')

                                <span
                                    class="inline-flex items-center rounded-full
                                           bg-red-50 px-2.5 py-1
                                           text-xs font-semibold text-red-700
                                           ring-1 ring-red-100">
                                    Pengeluaran
                                </span>

                            @else

                                <span
                                    class="inline-flex items-center rounded-full
                                           bg-gray-50 px-2.5 py-1
                                           text-xs font-semibold text-gray-600
                                           ring-1 ring-gray-100">
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
                        <td class="whitespace-nowrap px-6 py-4 text-right
                                   font-semibold text-gray-900">

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
                            class="px-6 py-12 text-center">

                            <div class="flex flex-col items-center justify-center">

                                <div class="flex h-12 w-12 items-center justify-center
                                            rounded-full bg-gray-100">

                                    <i class="bi bi-receipt text-xl text-gray-400"></i>

                                </div>

                                <p class="mt-3 text-sm font-medium text-gray-700">
                                    Tidak ada transaksi
                                </p>

                                <p class="mt-1 text-sm text-gray-500">
                                    Tidak ada transaksi pada periode dan kategori yang dipilih.
                                </p>

                            </div>

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>


    {{-- Pagination --}}
    @if ($transactions->hasPages())

        <div class="border-t border-gray-200 px-5 py-4 sm:px-6">

            {{ $transactions->links() }}

        </div>

    @endif

</div>
