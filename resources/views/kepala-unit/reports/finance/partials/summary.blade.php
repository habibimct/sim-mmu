<div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-4">

    {{-- Pemasukan --}}
    <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200">

        <div class="flex items-center justify-between">

            <div>
                <p class="text-sm font-medium text-gray-500">
                    Total Pemasukan
                </p>

                <p class="mt-2 text-xl font-semibold text-gray-800">
                    Rp {{ number_format($totalIncome, 0, ',', '.') }}
                </p>
            </div>

            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-100">
                <i class="bi bi-arrow-down-circle text-lg text-green-600"></i>
            </div>

        </div>

    </div>


    {{-- Pengeluaran --}}
    <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200">

        <div class="flex items-center justify-between">

            <div>
                <p class="text-sm font-medium text-gray-500">
                    Total Pengeluaran
                </p>

                <p class="mt-2 text-xl font-semibold text-gray-800">
                    Rp {{ number_format($totalExpense, 0, ',', '.') }}
                </p>
            </div>

            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-red-100">
                <i class="bi bi-arrow-up-circle text-lg text-red-600"></i>
            </div>

        </div>

    </div>


    {{-- Setoran --}}
    <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200">

        <div class="flex items-center justify-between">

            <div>
                <p class="text-sm font-medium text-gray-500">
                    Total Setoran
                </p>

                <p class="mt-2 text-xl font-semibold text-gray-800">
                    Rp {{ number_format($totalDeposit, 0, ',', '.') }}
                </p>
            </div>

            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-purple-100">
                <i class="bi bi-send-check text-lg text-purple-600"></i>
            </div>

        </div>

    </div>


    {{-- Saldo --}}
    <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200">

        <div class="flex items-center justify-between">

            <div>
                <p class="text-sm font-medium text-gray-500">
                    Saldo
                </p>

                <p class="mt-2 text-xl font-semibold text-gray-800">
                    Rp {{ number_format($netBalance, 0, ',', '.') }}
                </p>
            </div>

            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-100">
                <i class="bi bi-wallet2 text-lg text-indigo-600"></i>
            </div>

        </div>

    </div>

</div>
