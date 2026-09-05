<div class="bg-white rounded-2xl
           border border-gray-100
           shadow-sm overflow-hidden">

    {{-- =========================================================
    TABLE HEADER
    ========================================================== --}}

    <div
        class="px-5 py-4
               border-b border-gray-100
               flex flex-col sm:flex-row
               sm:items-center
               sm:justify-between
               gap-3">

        <div>

            <h2 class="font-semibold text-gray-800">
                Daftar Transaksi
            </h2>

            <p class="text-xs text-gray-500 mt-1">
                Seluruh transaksi keuangan yang dapat Anda monitor.
            </p>

        </div>


        <div class="flex items-center gap-2">

            <span
                class="hidden sm:inline-flex
                       items-center
                       px-3 py-2
                       rounded-lg
                       bg-gray-50
                       text-xs
                       text-gray-500">

                {{ $transactions->total() }} transaksi

            </span>

        </div>

    </div>


    {{-- =========================================================
    TABEL
    ========================================================== --}}

    <div class="overflow-x-auto">

        <table class="min-w-full text-sm">

            <thead class="bg-gray-50">

                <tr>

                    {{-- ID --}}
                    <th
                        class="px-5 py-3
                               text-left
                               text-xs font-semibold
                               text-gray-500 uppercase
                               whitespace-nowrap">

                        ID Transaksi

                    </th>


                    {{-- Tanggal --}}
                    <th
                        class="px-5 py-3
                               text-left
                               text-xs font-semibold
                               text-gray-500 uppercase
                               whitespace-nowrap">

                        Tanggal

                    </th>


                    {{-- Kategori --}}
                    <th
                        class="px-5 py-3
                               text-left
                               text-xs font-semibold
                               text-gray-500 uppercase">

                        Kategori

                    </th>


                    {{-- Keterangan --}}
                    <th
                        class="px-5 py-3
                               text-left
                               text-xs font-semibold
                               text-gray-500 uppercase">

                        Keterangan

                    </th>


                    {{-- Metode --}}
                    <th
                        class="px-5 py-3
                               text-left
                               text-xs font-semibold
                               text-gray-500 uppercase
                               whitespace-nowrap">

                        Metode

                    </th>


                    {{-- Jumlah --}}
                    <th
                        class="px-5 py-3
                               text-right
                               text-xs font-semibold
                               text-gray-500 uppercase
                               whitespace-nowrap">

                        Jumlah

                    </th>


                    {{-- Status --}}
                    <th
                        class="px-5 py-3
                               text-center
                               text-xs font-semibold
                               text-gray-500 uppercase">

                        Status

                    </th>

                </tr>

            </thead>


            <tbody class="divide-y divide-gray-100">

                @forelse ($transactions as $transaction)
                    <tr class="hover:bg-gray-50 transition">

                        {{-- =================================================
                        ID TRANSAKSI
                        ================================================== --}}

                        <td class="px-5 py-4 whitespace-nowrap">

                            <span
                                class="font-mono
                                       text-xs
                                       text-gray-600">

                                #{{ $transaction->id }}

                            </span>

                        </td>


                        {{-- =================================================
                        TANGGAL
                        ================================================== --}}

                        <td class="px-5 py-4 whitespace-nowrap">

                            {{ $transaction->transaction_date->format('d/m/Y') }}

                        </td>


                        {{-- =================================================
                        KATEGORI
                        ================================================== --}}

                        <td class="px-5 py-4">

                            <span class="font-medium text-gray-800">

                                {{ $transaction->category ?: '-' }}

                            </span>

                        </td>


                        {{-- =================================================
                        KETERANGAN
                        ================================================== --}}

                        <td class="px-5 py-4">

                            <p class="text-gray-600 max-w-xs">

                                {{ $transaction->description ?: '-' }}

                            </p>

                        </td>


                        {{-- =================================================
                        METODE
                        ================================================== --}}

                        <td class="px-5 py-4 whitespace-nowrap">

                            @switch($transaction->payment_method)
                                @case('cash')
                                    Tunai
                                @break

                                @case('bank_transfer')
                                    Transfer Bank
                                @break

                                @case('online')
                                    Online
                                @break

                                @default
                                    {{ $transaction->payment_method ?: '-' }}
                            @endswitch

                        </td>


                        {{-- =================================================
                        JUMLAH
                        ================================================== --}}

                        <td
                            class="px-5 py-4
                                   text-right
                                   whitespace-nowrap">

                            <span
                                class="font-semibold
                                    {{ $transaction->type === 'income' ? 'text-emerald-600' : 'text-red-600' }}">

                                {{ $transaction->type === 'income' ? '+' : '-' }}

                                Rp
                                {{ number_format($transaction->amount, 0, ',', '.') }}

                            </span>

                        </td>


                        {{-- =================================================
                        STATUS
                        ================================================== --}}

                        <td class="px-5 py-4 text-center">

                            @switch($transaction->status)
                                @case('confirmed')
                                    <span
                                        class="inline-flex
                                               items-center
                                               px-2.5 py-1
                                               rounded-full
                                               text-xs
                                               font-medium
                                               bg-emerald-50
                                               text-emerald-700">

                                        Dikonfirmasi

                                    </span>
                                @break

                                @case('pending')
                                    <span
                                        class="inline-flex
                                               items-center
                                               px-2.5 py-1
                                               rounded-full
                                               text-xs
                                               font-medium
                                               bg-amber-50
                                               text-amber-700">

                                        Menunggu

                                    </span>
                                @break

                                @case('cancelled')
                                    <span
                                        class="inline-flex
                                               items-center
                                               px-2.5 py-1
                                               rounded-full
                                               text-xs
                                               font-medium
                                               bg-red-50
                                               text-red-700">

                                        Dibatalkan

                                    </span>
                                @break

                                @default
                                    <span
                                        class="inline-flex
                                               items-center
                                               px-2.5 py-1
                                               rounded-full
                                               text-xs
                                               font-medium
                                               bg-gray-100
                                               text-gray-600">

                                        {{ ucfirst($transaction->status ?? '-') }}

                                    </span>
                            @endswitch

                        </td>

                    </tr>

                    @empty

                        <tr>

                            <td colspan="7" class="px-5 py-12
                                   text-center">

                                <p class="text-sm text-gray-600">

                                    Belum ada transaksi.

                                </p>

                            </td>

                        </tr>
                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- =========================================================
    PAGINATION
    ========================================================== --}}

        @if ($transactions->hasPages())
            <div class="px-5 py-4
                   border-t border-gray-100">

                {{ $transactions->links() }}

            </div>
        @endif

    </div>
