<div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4">

    {{-- Pemasukan --}}
    <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-200
                transition hover:-translate-y-0.5 hover:shadow-md">

        <div class="flex items-start justify-between">

            <div class="min-w-0">
                <p class="text-sm font-medium text-gray-500">
                    Total Pemasukan
                </p>

                <p class="mt-2 text-xl font-bold tracking-tight text-gray-900">
                    Rp {{ number_format($totalIncome, 0, ',', '.') }}
                </p>
            </div>

            <div class="ml-4 flex h-11 w-11 shrink-0 items-center justify-center
                        rounded-xl bg-green-50 ring-1 ring-green-100">

                <i class="bi bi-arrow-down-circle text-xl text-green-600"></i>

            </div>

        </div>

    </div>


    {{-- Pengeluaran --}}
    <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-200
                transition hover:-translate-y-0.5 hover:shadow-md">

        <div class="flex items-start justify-between">

            <div class="min-w-0">
                <p class="text-sm font-medium text-gray-500">
                    Total Pengeluaran
                </p>

                <p class="mt-2 text-xl font-bold tracking-tight text-gray-900">
                    Rp {{ number_format($totalExpense, 0, ',', '.') }}
                </p>
            </div>

            <div class="ml-4 flex h-11 w-11 shrink-0 items-center justify-center
                        rounded-xl bg-red-50 ring-1 ring-red-100">

                <i class="bi bi-arrow-up-circle text-xl text-red-600"></i>

            </div>

        </div>

    </div>


    {{-- Setoran --}}
    <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-200
                transition hover:-translate-y-0.5 hover:shadow-md">

        <div class="flex items-start justify-between">

            <div class="min-w-0">
                <p class="text-sm font-medium text-gray-500">
                    Total Setoran
                </p>

                <p class="mt-2 text-xl font-bold tracking-tight text-gray-900">
                    Rp {{ number_format($totalDeposit, 0, ',', '.') }}
                </p>
            </div>

            <div class="ml-4 flex h-11 w-11 shrink-0 items-center justify-center
                        rounded-xl bg-purple-50 ring-1 ring-purple-100">

                <i class="bi bi-send-check text-xl text-purple-600"></i>

            </div>

        </div>

    </div>


    {{-- Saldo --}}
    <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-200
                transition hover:-translate-y-0.5 hover:shadow-md">

        <div class="flex items-start justify-between">

            <div class="min-w-0">
                <p class="text-sm font-medium text-gray-500">
                    Saldo
                </p>

                <p class="mt-2 text-xl font-bold tracking-tight text-gray-900">
                    Rp {{ number_format($netBalance, 0, ',', '.') }}
                </p>
            </div>

            <div class="ml-4 flex h-11 w-11 shrink-0 items-center justify-center
                        rounded-xl bg-indigo-50 ring-1 ring-indigo-100">

                <i class="bi bi-wallet2 text-xl text-indigo-600"></i>

            </div>

        </div>

    </div>

</div>
