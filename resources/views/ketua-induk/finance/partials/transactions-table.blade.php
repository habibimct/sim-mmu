{{-- ==========================================================
TABEL TRANSAKSI KEUANGAN
=========================================================== --}}

<div class="overflow-hidden rounded-lg bg-white shadow-sm">

    <div class="overflow-x-auto">

        <table class="min-w-full divide-y divide-gray-200">

            <thead class="bg-gray-50">

                <tr>

                    <th
                        class="px-5 py-3 text-left text-xs
                               font-medium uppercase text-gray-500"
                    >
                        ID
                    </th>

                    <th
                        class="px-5 py-3 text-left text-xs
                               font-medium uppercase text-gray-500"
                    >
                        Tanggal
                    </th>

                    <th
                        class="px-5 py-3 text-left text-xs
                               font-medium uppercase text-gray-500"
                    >
                        Organisasi
                    </th>

                    <th
                        class="px-5 py-3 text-left text-xs
                               font-medium uppercase text-gray-500"
                    >
                        Jenis
                    </th>

                    <th
                        class="px-5 py-3 text-left text-xs
                               font-medium uppercase text-gray-500"
                    >
                        Kategori
                    </th>

                    <th
                        class="px-5 py-3 text-right text-xs
                               font-medium uppercase text-gray-500"
                    >
                        Jumlah
                    </th>

                    <th
                        class="px-5 py-3 text-center text-xs
                               font-medium uppercase text-gray-500"
                    >
                        Status
                    </th>

                </tr>

            </thead>


            <tbody class="divide-y divide-gray-200">

                @forelse ($transactions as $transaction)

                    <tr class="hover:bg-gray-50">

                        {{-- ID --}}

                        <td class="whitespace-nowrap px-5 py-4 text-sm
                                   font-mono text-gray-500">

                            #{{ $transaction->id }}

                        </td>


                        {{-- Tanggal --}}

                        <td class="whitespace-nowrap px-5 py-4 text-sm
                                   text-gray-600">

                            {{ $transaction->transaction_date->format('d/m/Y') }}

                        </td>


                        {{-- Organisasi --}}

                        <td class="px-5 py-4 text-sm font-medium text-gray-800">

                            {{ $transaction->organization->name }}

                        </td>


                        {{-- Jenis --}}

                        <td class="px-5 py-4 text-sm">

                            @if ($transaction->type === 'income')

                                <span class="font-medium text-green-600">
                                    Pemasukan
                                </span>

                            @else

                                <span class="font-medium text-red-600">
                                    Pengeluaran
                                </span>

                            @endif

                        </td>


                        {{-- Kategori --}}

                        <td class="px-5 py-4 text-sm text-gray-600">

                            {{ $transaction->category ?: '-' }}

                        </td>


                        {{-- Jumlah --}}

                        <td
                            class="whitespace-nowrap px-5 py-4
                                   text-right text-sm font-medium"
                        >

                            Rp
                            {{ number_format(
                                $transaction->amount,
                                0,
                                ',',
                                '.'
                            ) }}

                        </td>


                        {{-- Status --}}

                        <td class="px-5 py-4 text-center">

                            @if ($transaction->status === 'confirmed')

                                <span
                                    class="inline-flex rounded-full
                                           bg-green-100 px-2.5 py-1
                                           text-xs font-medium
                                           text-green-700"
                                >
                                    Dikonfirmasi
                                </span>


                            @elseif ($transaction->status === 'pending')

                                <span
                                    class="inline-flex rounded-full
                                           bg-yellow-100 px-2.5 py-1
                                           text-xs font-medium
                                           text-yellow-700"
                                >
                                    Menunggu
                                </span>


                            @elseif ($transaction->status === 'cancelled')

                                <span
                                    class="inline-flex rounded-full
                                           bg-red-100 px-2.5 py-1
                                           text-xs font-medium
                                           text-red-700"
                                >
                                    Dibatalkan
                                </span>


                            @elseif ($transaction->status === 'rejected')

                                <span
                                    class="inline-flex rounded-full
                                           bg-red-100 px-2.5 py-1
                                           text-xs font-medium
                                           text-red-700"
                                >
                                    Ditolak
                                </span>


                            @else

                                <span
                                    class="inline-flex rounded-full
                                           bg-gray-100 px-2.5 py-1
                                           text-xs font-medium
                                           text-gray-600"
                                >
                                    {{ ucfirst($transaction->status ?? '-') }}
                                </span>

                            @endif

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="7"
                            class="px-5 py-8 text-center
                                   text-sm text-gray-500"
                        >
                            Tidak ada transaksi sesuai filter.
                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>


    {{-- Pagination --}}

    <div class="border-t border-gray-200 px-5 py-4">

        {{ $transactions->links() }}

    </div>

</div>
