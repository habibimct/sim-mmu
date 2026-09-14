<div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">

    <div class="border-b border-gray-200 px-5 py-4 sm:px-6">

        <h3 class="text-base font-semibold text-gray-900">
            Setoran Unit ke PMUB
        </h3>

        <p class="mt-1 text-sm text-gray-500">
            Rekap setoran unit kepada PMUB dalam periode yang dipilih.
        </p>

    </div>

    <div class="grid grid-cols-1 gap-5 p-5 sm:grid-cols-2 sm:p-6">

        {{-- Total Setoran --}}
        <div class="rounded-xl border border-gray-200 bg-gray-50/50 p-4
                    transition hover:border-gray-300 hover:shadow-sm">

            <div class="flex items-center justify-between">

                <div class="min-w-0">
                    <p class="text-sm font-medium text-gray-500">
                        Total Setoran
                    </p>

                    <p class="mt-1 text-lg font-bold tracking-tight text-gray-900">
                        Rp {{ number_format($totalDeposit, 0, ',', '.') }}
                    </p>
                </div>

                <div class="ml-4 flex h-11 w-11 shrink-0 items-center justify-center
                            rounded-xl bg-purple-50">

                    <i class="bi bi-send-check text-xl text-purple-600"></i>

                </div>

            </div>

        </div>


        {{-- Jumlah Transaksi Setoran --}}
        <div class="rounded-xl border border-gray-200 bg-gray-50/50 p-4
                    transition hover:border-gray-300 hover:shadow-sm">

            <div class="flex items-center justify-between">

                <div class="min-w-0">
                    <p class="text-sm font-medium text-gray-500">
                        Jumlah Transaksi
                    </p>

                    <p class="mt-1 text-lg font-bold tracking-tight text-gray-900">
                        {{ $depositCount }} transaksi
                    </p>
                </div>

                <div class="ml-4 flex h-11 w-11 shrink-0 items-center justify-center
                            rounded-xl bg-blue-50">

                    <i class="bi bi-receipt text-xl text-blue-600"></i>

                </div>

            </div>

        </div>

    </div>

</div>
