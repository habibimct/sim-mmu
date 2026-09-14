<div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-200">

    <div class="border-b border-gray-200 px-6 py-4">

        <h3 class="text-base font-semibold text-gray-800">
            Setoran Unit ke PMUB
        </h3>

        <p class="mt-1 text-sm text-gray-500">
            Rekap setoran unit kepada PMUB dalam periode yang dipilih.
        </p>

    </div>

    <div class="grid grid-cols-1 gap-5 p-6 sm:grid-cols-2">

        {{-- Total Setoran --}}
        <div class="rounded-lg border border-gray-200 p-4">

            <div class="flex items-center justify-between">

                <div>
                    <p class="text-sm text-gray-500">
                        Total Setoran
                    </p>

                    <p class="mt-1 text-lg font-semibold text-gray-800">
                        Rp {{ number_format($totalDeposit, 0, ',', '.') }}
                    </p>
                </div>

                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-purple-100">
                    <i class="bi bi-send-check text-lg text-purple-600"></i>
                </div>

            </div>

        </div>


        {{-- Jumlah Transaksi Setoran --}}
        <div class="rounded-lg border border-gray-200 p-4">

            <div class="flex items-center justify-between">

                <div>
                    <p class="text-sm text-gray-500">
                        Jumlah Transaksi
                    </p>

                    <p class="mt-1 text-lg font-semibold text-gray-800">
                        {{ $depositCount }} transaksi
                    </p>
                </div>

                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-100">
                    <i class="bi bi-receipt text-lg text-blue-600"></i>
                </div>

            </div>

        </div>

    </div>

</div>
