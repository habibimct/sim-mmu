{{-- Modal Payment Notifications (detail student payment) --}}
<div id="paymentNotificationModal" class="fixed inset-0 z-50 hidden" aria-labelledby="paymentNotificationModalTitle"
    role="dialog" aria-modal="true">

    <div class="absolute inset-0 bg-black/50" data-payment-modal-close></div>


    <div class="relative flex min-h-full items-center justify-center p-4">

        <div class="w-full max-w-2xl rounded-xl bg-white shadow-xl">

            {{-- HEADER --}}

            <div class="flex items-center justify-between
                       border-b px-5 py-4">

                <div>

                    <h2 id="paymentNotificationModalTitle" class="text-lg font-semibold text-gray-900">
                        Detail Pembayaran
                    </h2>

                    <p id="paymentModalNumber" class="mt-1 text-sm text-gray-500">
                        -
                    </p>

                </div>

                <button type="button" data-payment-modal-close class="text-gray-400 hover:text-gray-600">
                    ✕
                </button>

            </div>


            {{-- BODY --}}

            <div class="px-5 py-5">

                <div id="paymentModalLoading" class="py-10 text-center">

                    <div class="text-sm text-gray-500">
                        Memuat detail pembayaran...
                    </div>

                </div>


                <div id="paymentModalError"
                    class="hidden rounded-lg
                           border border-red-200
                           bg-red-50 p-4
                           text-sm text-red-700">
                </div>


                <div id="paymentModalContent" class="hidden">

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">

                        <div>

                            <div class="text-xs text-gray-500">
                                Status
                            </div>

                            <div id="paymentModalStatus" class="mt-1"></div>

                        </div>


                        <div>

                            <div class="text-xs text-gray-500">
                                Nominal
                            </div>

                            <div id="paymentModalAmount" class="mt-1 font-semibold text-gray-900">
                                -
                            </div>

                        </div>


                        <div>

                            <div class="text-xs text-gray-500">
                                Tanggal
                            </div>

                            <div id="paymentModalDate" class="mt-1 font-medium text-gray-800">
                                -
                            </div>

                        </div>


                        <div>

                            <div class="text-xs text-gray-500">
                                Metode
                            </div>

                            <div id="paymentModalMethod" class="mt-1 font-medium text-gray-800">
                                -
                            </div>

                        </div>

                    </div>


                    <div class="mt-5">

                        <div class="mb-2 text-sm font-semibold text-gray-800">
                            Alokasi Tagihan
                        </div>

                        <div id="paymentModalAllocations" class="overflow-hidden rounded-lg border"></div>

                    </div>

                </div>

            </div>


            {{-- FOOTER --}}

            <div class="flex items-center justify-end gap-2
           border-t px-5 py-4">
                <div>
                    <span id="paymentModalConfirmInfo" class="hidden text-sm text-gray-500"></span>
                </div>

                <button type="button" data-payment-modal-close
                    class="rounded-md border
                    border-gray-300
                    px-4 py-2
                    text-sm font-medium
                    text-gray-700
                    hover:bg-gray-50">
                    Tutup
                </button>

                <button type="button" id="paymentModalConfirmButton"
                    class="rounded-md
                    bg-green-600
                    px-4 py-2
                    text-sm font-medium
                    text-white
                    hover:bg-green-700">
                    Konfirmasi Pembayaran
                </button>

                <button type="button" id="paymentModalCancelButton"
                    class="hidden rounded-md
                    bg-red-600
                    px-4 py-2
                    text-sm font-medium
                    text-white
                    hover:bg-red-700
                    disabled:cursor-not-allowed
                    disabled:opacity-50">
                    Batal Konfirmasi
                </button>

            </div>
        </div>
    </div>
</div>



<script>
    document.addEventListener('DOMContentLoaded', function() {

        const modal = document.getElementById(
            'paymentNotificationModal'
        );

        if (!modal) {
            return;
        }

        const loading = document.getElementById(
            'paymentModalLoading'
        );

        const error = document.getElementById(
            'paymentModalError'
        );

        const content = document.getElementById(
            'paymentModalContent'
        );


        function formatRupiah(value) {

            return new Intl.NumberFormat(
                'id-ID'
            ).format(
                Number(value) || 0
            );

        }


        function methodLabel(method) {

            switch (method) {

                case 'cash':
                    return 'Tunai';

                case 'bank_transfer':
                    return 'Transfer Bank';

                case 'online':
                    return 'Online';

                default:
                    return method ?? '-';

            }

        }


        function statusBadge(status) {

            switch (status) {

                case 'pending':

                    return `
                    <span class="inline-flex items-center
                                 rounded-full
                                 bg-yellow-100
                                 px-2.5 py-1
                                 text-xs font-medium
                                 text-yellow-800">
                        Menunggu Konfirmasi
                    </span>
                `;

                case 'confirmed':

                    return `
                    <span class="inline-flex items-center
                                 rounded-full
                                 bg-green-100
                                 px-2.5 py-1
                                 text-xs font-medium
                                 text-green-800">
                        Dikonfirmasi
                    </span>
                `;

                case 'cancelled':

                    return `
                    <span class="inline-flex items-center
                                 rounded-full
                                 bg-red-100
                                 px-2.5 py-1
                                 text-xs font-medium
                                 text-red-800">
                        Dibatalkan
                    </span>
                `;

                default:

                    return `
                    <span class="inline-flex items-center
                                 rounded-full
                                 bg-gray-100
                                 px-2.5 py-1
                                 text-xs font-medium
                                 text-gray-700">
                        ${status ?? '-'}
                    </span>
                `;

            }

        }


        function resetModal() {

            loading.classList.remove('hidden');

            error.classList.add('hidden');

            error.textContent = '';

            content.classList.add('hidden');

            document.getElementById(
                'paymentModalNumber'
            ).textContent = '-';

            document.getElementById(
                'paymentModalStatus'
            ).innerHTML = '';

            document.getElementById(
                'paymentModalAmount'
            ).textContent = '-';

            document.getElementById(
                'paymentModalDate'
            ).textContent = '-';

            document.getElementById(
                'paymentModalMethod'
            ).textContent = '-';

            document.getElementById(
                'paymentModalAllocations'
            ).innerHTML = '';

        }


        async function loadPayment(
            paymentId
        ) {

            resetModal();


            modal.classList.remove('hidden');

            document.body.classList.add('overflow-hidden');


            try {

                const response = await fetch(
                    `{{ url('admin/finance/payments') }}/${paymentId}/detail`, {
                        method: 'GET',

                        headers: {
                            'Accept': 'application/json',

                            'X-Requested-With': 'XMLHttpRequest',

                        },
                    }
                );


                if (!response.ok) {

                    throw new Error(
                        'Detail pembayaran tidak dapat dimuat.'
                    );

                }


                const payment =
                    await response.json();

                const confirmButton =
                    document.getElementById(
                        'paymentModalConfirmButton'
                    );

                const cancelButton =
                    document.getElementById(
                        'paymentModalCancelButton'
                    );


                if (
                    payment.status === 'pending'
                ) {

                    confirmButton.classList.remove(
                        'hidden'
                    );

                    cancelButton.classList.add(
                        'hidden'
                    );

                } else if (
                    payment.status === 'confirmed'
                ) {

                    confirmButton.classList.add(
                        'hidden'
                    );

                    cancelButton.classList.remove(
                        'hidden'
                    );

                } else {

                    confirmButton.classList.add(
                        'hidden'
                    );

                    cancelButton.classList.add(
                        'hidden'
                    );

                }

                confirmButton.dataset.paymentId =
                    payment.id;

                cancelButton.dataset.paymentId =
                    payment.id;


                document
                    .getElementById(
                        'paymentModalConfirmButton'
                    )
                    .dataset.paymentId =
                    payment.id;

                document.getElementById(
                        'paymentModalNumber'
                    ).textContent =
                    payment.payment_number ?? '-';


                document.getElementById(
                        'paymentModalStatus'
                    ).innerHTML =
                    statusBadge(
                        payment.status
                    );


                document.getElementById(
                        'paymentModalAmount'
                    ).textContent =
                    'Rp ' +
                    formatRupiah(
                        payment.amount
                    );


                document.getElementById(
                        'paymentModalDate'
                    ).textContent =
                    payment.payment_date ?? '-';


                document.getElementById(
                        'paymentModalMethod'
                    ).textContent =
                    methodLabel(
                        payment.payment_method
                    );


                const allocations =
                    document.getElementById(
                        'paymentModalAllocations'
                    );


                if (
                    !payment.allocations ||
                    payment.allocations.length === 0
                ) {

                    allocations.innerHTML = `
                    <div class="px-4 py-5 text-center
                                text-sm text-gray-500">
                        Belum ada alokasi tagihan.
                    </div>
                `;

                } else {

                    let html = `
                    <div class="overflow-x-auto">

                        <table class="min-w-full
                                      divide-y
                                      divide-gray-200">

                            <thead class="bg-gray-50">

                                <tr>

                                    <th class="px-4 py-3
                                               text-left
                                               text-xs
                                               font-semibold
                                               uppercase
                                               tracking-wider
                                               text-gray-500">
                                        Siswa
                                    </th>

                                    <th class="px-4 py-3
                                               text-left
                                               text-xs
                                               font-semibold
                                               uppercase
                                               tracking-wider
                                               text-gray-500">
                                        Tagihan
                                    </th>

                                    <th class="px-4 py-3
                                               text-left
                                               text-xs
                                               font-semibold
                                               uppercase
                                               tracking-wider
                                               text-gray-500">
                                        Periode
                                    </th>

                                    <th class="px-4 py-3
                                               text-right
                                               text-xs
                                               font-semibold
                                               uppercase
                                               tracking-wider
                                               text-gray-500">
                                        Nominal
                                    </th>

                                </tr>

                            </thead>

                            <tbody class="divide-y
                                         divide-gray-200
                                         bg-white">
                `;


                    payment.allocations.forEach(
                        function(allocation) {

                            html += `
                            <tr>

                                <td class="px-4 py-3">

                                    <div class="font-medium
                                                text-gray-800">
                                        ${allocation.student_name ?? '-'}
                                    </div>

                                    <div class="text-xs
                                                text-gray-500">
                                        NIS:
                                        ${allocation.nis ?? '-'}
                                    </div>

                                </td>

                                <td class="px-4 py-3
                                           text-sm
                                           text-gray-700">
                                    ${allocation.bill_type ?? '-'}
                                </td>

                                <td class="px-4 py-3
                                           text-sm
                                           text-gray-700">
                                    ${allocation.period ?? '-'}
                                </td>

                                <td class="px-4 py-3
                                           text-right
                                           text-sm
                                           font-semibold
                                           text-gray-800">
                                    Rp
                                    ${formatRupiah(
                                        allocation.amount
                                    )}
                                </td>

                            </tr>
                        `;

                        }
                    );


                    html += `
                            </tbody>

                        </table>

                    </div>
                `;

                    allocations.innerHTML = html;

                }


                loading.classList.add('hidden');

                content.classList.remove('hidden');


            } catch (exception) {

                loading.classList.add('hidden');

                error.textContent =
                    exception.message;

                error.classList.remove('hidden');

            }

        }


        /*
        |--------------------------------------------------------------------------
        | Buka modal
        |--------------------------------------------------------------------------
        */

        document.addEventListener(
            'click',
            function(event) {

                const button =
                    event.target.closest(
                        '.btn-payment-notification-detail'
                    );


                if (!button) {
                    return;
                }


                const paymentId =
                    button.dataset.paymentId;


                if (!paymentId) {
                    return;
                }


                loadPayment(
                    paymentId
                );

            }
        );


        /*
        |--------------------------------------------------------------------------
        | Tutup modal
        |--------------------------------------------------------------------------
        */

        document.addEventListener(
            'click',
            function(event) {

                if (
                    event.target.closest(
                        '[data-payment-modal-close]'
                    )
                ) {

                    modal.classList.add('hidden');

                    document.body.classList.remove(
                        'overflow-hidden'
                    );

                }

            }
        );


        /*
        |--------------------------------------------------------------------------
        | ESC
        |--------------------------------------------------------------------------
        */

        document.addEventListener(
            'keydown',
            function(event) {

                if (
                    event.key === 'Escape' &&
                    !modal.classList.contains('hidden')
                ) {

                    modal.classList.add('hidden');

                    document.body.classList.remove(
                        'overflow-hidden'
                    );

                }

            }
        );

    });




    document.addEventListener(
        'click',
        async function(event) {

            const button =
                event.target.closest(
                    '#paymentModalCancelButton'
                );

            if (!button) {
                return;
            }

            const paymentId =
                button.dataset.paymentId;

            if (!paymentId) {

                alert(
                    'ID pembayaran tidak ditemukan.'
                );

                return;
            }


            const reason =
                prompt(
                    'Masukkan alasan pembatalan konfirmasi:'
                );


            if (
                reason === null ||
                reason.trim() === ''
            ) {

                return;

            }


            button.disabled = true;

            button.textContent =
                'Memproses...';


            try {

                const response =
                    await fetch(
                        `{{ url('admin/finance/payments') }}/${paymentId}/cancel`, {
                            method: 'POST',

                            headers: {

                                'Accept': 'application/json',

                                'Content-Type': 'application/json',

                                'X-CSRF-TOKEN': '{{ csrf_token() }}',

                                'X-Requested-With': 'XMLHttpRequest',

                            },

                            body: JSON.stringify({
                                cancellation_reason: reason.trim(),
                            }),
                        }
                    );


                const result =
                    await response.json();


                if (!response.ok) {

                    throw new Error(
                        result.message ??
                        'Pembatalan konfirmasi gagal.'
                    );

                }


                alert(
                    result.message
                );


                window.location.reload();


            } catch (error) {

                alert(
                    error.message
                );

                button.disabled = false;

                button.textContent =
                    'Batal Konfirmasi';

            }

        }
    );


    document.addEventListener('click', async function(event) {

        const button =
            event.target.closest(
                '#paymentModalConfirmButton'
            );

        if (!button) {
            return;
        }

        const paymentId =
            button.dataset.paymentId;

        if (!paymentId) {

            alert(
                'ID pembayaran tidak ditemukan.'
            );

            return;
        }


        button.disabled = true;

        button.textContent =
            'Memproses...';


        try {

            const response =
                await fetch(
                    `{{ url('admin/finance/payments') }}/${paymentId}/confirm`, {
                        method: 'POST',

                        headers: {
                            'Accept': 'application/json',

                            'Content-Type': 'application/json',

                            'X-CSRF-TOKEN': '{{ csrf_token() }}',

                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    }
                );


            const result =
                await response.json();


            if (!response.ok) {

                throw new Error(
                    result.message ??
                    'Pembayaran gagal dikonfirmasi.'
                );

            }


            alert(
                result.message
            );


            /*
            |--------------------------------------------------------------------------
            | Tutup modal
            |--------------------------------------------------------------------------
            */

            document
                .getElementById(
                    'paymentNotificationModal'
                )
                .classList.add(
                    'hidden'
                );

            document.body.classList.remove(
                'overflow-hidden'
            );


            /*
            |--------------------------------------------------------------------------
            | Refresh halaman
            |--------------------------------------------------------------------------
            */

            window.location.reload();


        } catch (error) {

            alert(
                error.message
            );

            button.disabled = false;

            button.textContent =
                'Konfirmasi Pembayaran';

        }

    });
</script>
