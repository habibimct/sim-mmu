<div class="modal fade" id="modalPaymentDetail" tabindex="-1" aria-labelledby="modalPaymentDetailLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header">

                <div>

                    <h5 class="modal-title" id="modalPaymentDetailLabel">
                        Detail Pembayaran
                    </h5>

                    <div id="paymentDetailNumber" class="text-muted small">
                        -
                    </div>

                </div>

                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>

            </div>


            <div class="modal-body">

                {{-- Loading --}}

                <div id="paymentDetailLoading" class="text-center py-5">

                    <div class="spinner-border" role="status"></div>

                    <div class="text-muted mt-2">
                        Memuat detail pembayaran...
                    </div>

                </div>


                {{-- Error --}}

                <div id="paymentDetailError" class="alert alert-danger d-none"></div>


                {{-- Content --}}

                <div id="paymentDetailContent" class="d-none">

                    <div class="row g-3 mb-4">

                        <div class="col-md-6">

                            <div class="text-muted small">
                                Status
                            </div>

                            <div id="paymentDetailStatus">
                                -
                            </div>

                        </div>


                        <div class="col-md-6">

                            <div class="text-muted small">
                                Nominal
                            </div>

                            <div id="paymentDetailAmount" class="fs-4 fw-bold">
                                Rp 0
                            </div>

                        </div>


                        <div class="col-md-6">

                            <div class="text-muted small">
                                Unit
                            </div>

                            <div id="paymentDetailOrganization" class="fw-semibold">
                                -
                            </div>

                        </div>


                        <div class="col-md-6">

                            <div class="text-muted small">
                                Tanggal Pembayaran
                            </div>

                            <div id="paymentDetailDate" class="fw-semibold">
                                -
                            </div>

                        </div>


                        <div class="col-md-6">

                            <div class="text-muted small">
                                Metode Pembayaran
                            </div>

                            <div id="paymentDetailMethod" class="fw-semibold">
                                -
                            </div>

                        </div>


                        <div class="col-md-6">

                            <div class="text-muted small">
                                Dicatat Oleh
                            </div>

                            <div id="paymentDetailCreator" class="fw-semibold">
                                -
                            </div>

                        </div>

                    </div>


                    {{-- Alokasi --}}

                    <div class="border rounded">

                        <div class="p-3 border-bottom fw-semibold">

                            Alokasi Tagihan

                        </div>

                        <div class="table-responsive">

                            <table class="table table-sm table-hover align-middle mb-0">

                                <thead>

                                    <tr>

                                        <th>
                                            Siswa
                                        </th>

                                        <th>
                                            Tagihan
                                        </th>

                                        <th>
                                            Periode
                                        </th>

                                        <th class="text-end">
                                            Nominal
                                        </th>

                                    </tr>

                                </thead>

                                <tbody id="paymentDetailAllocations">

                                </tbody>

                            </table>

                        </div>

                    </div>


                    {{-- Keterangan --}}

                    <div id="paymentDetailDescriptionWrapper" class="mt-3 d-none">

                        <div class="text-muted small">
                            Keterangan
                        </div>

                        <div id="paymentDetailDescription" class="mt-1"></div>

                    </div>

                </div>

            </div>


            <div class="modal-footer">

                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Tutup
                </button>

            </div>

        </div>

    </div>
</div>



@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const modal =
                document.getElementById(
                    'modalPaymentDetail'
                );

            if (!modal) {
                return;
            }


            const loading =
                document.getElementById(
                    'paymentDetailLoading'
                );

            const error =
                document.getElementById(
                    'paymentDetailError'
                );

            const content =
                document.getElementById(
                    'paymentDetailContent'
                );


            function formatRupiah(
                value
            ) {

                return new Intl.NumberFormat(
                    'id-ID'
                ).format(
                    Number(value) || 0
                );

            }


            function paymentMethodLabel(
                method
            ) {

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


            function statusBadge(
                status
            ) {

                switch (status) {

                    case 'pending':

                        return `
                        <span class="badge bg-warning text-dark">
                            Menunggu Konfirmasi
                        </span>
                    `;

                    case 'confirmed':

                        return `
                        <span class="badge bg-success">
                            Dikonfirmasi
                        </span>
                    `;

                    case 'failed':

                        return `
                        <span class="badge bg-danger">
                            Gagal
                        </span>
                    `;

                    case 'cancelled':

                        return `
                        <span class="badge bg-secondary">
                            Dibatalkan
                        </span>
                    `;

                    default:

                        return `
                        <span class="badge bg-light text-dark">
                            ${status ?? '-'}
                        </span>
                    `;

                }

            }


            function resetModal() {

                loading.classList.remove(
                    'd-none'
                );

                error.classList.add(
                    'd-none'
                );

                error.textContent = '';

                content.classList.add(
                    'd-none'
                );

            }


            async function loadPaymentDetail(
                paymentId
            ) {

                resetModal();


                const url =
                    `{{ url('admin/finance/payments') }}/${paymentId}/detail`;


                try {

                    const response =
                        await fetch(
                            url, {
                                headers: {
                                    'Accept': 'application/json',

                                    'X-Requested-With': 'XMLHttpRequest',
                                }
                            }
                        );


                    if (!response.ok) {

                        throw new Error(
                            'Detail pembayaran tidak dapat dimuat.'
                        );

                    }


                    const payment =
                        await response.json();


                    document.getElementById(
                            'paymentDetailNumber'
                        ).textContent =
                        payment.payment_number ?? '-';


                    document.getElementById(
                            'paymentDetailStatus'
                        ).innerHTML =
                        statusBadge(
                            payment.status
                        );


                    document.getElementById(
                            'paymentDetailAmount'
                        ).textContent =
                        'Rp ' +
                        formatRupiah(
                            payment.amount
                        );


                    document.getElementById(
                            'paymentDetailOrganization'
                        ).textContent =
                        payment.organization ?? '-';


                    document.getElementById(
                            'paymentDetailDate'
                        ).textContent =
                        payment.payment_date ?? '-';


                    document.getElementById(
                            'paymentDetailMethod'
                        ).textContent =
                        paymentMethodLabel(
                            payment.payment_method
                        );


                    document.getElementById(
                            'paymentDetailCreator'
                        ).textContent =
                        payment.creator ?? '-';


                    const allocations =
                        document.getElementById(
                            'paymentDetailAllocations'
                        );


                    allocations.innerHTML = '';


                    if (
                        !payment.allocations ||
                        payment.allocations.length === 0
                    ) {

                        allocations.innerHTML = `
                        <tr>
                            <td
                                colspan="4"
                                class="text-center text-muted py-4"
                            >
                                Belum ada alokasi.
                            </td>
                        </tr>
                    `;

                    } else {

                        payment.allocations.forEach(
                            function(allocation) {

                                allocations.innerHTML += `

                                <tr>

                                    <td>

                                        <div class="fw-semibold">
                                            ${allocation.student_name ?? '-'}
                                        </div>

                                        <div class="small text-muted">
                                            NIS:
                                            ${allocation.nis ?? '-'}
                                        </div>

                                    </td>

                                    <td>
                                        ${allocation.bill_type ?? '-'}
                                    </td>

                                    <td>
                                        ${allocation.period ?? '-'}
                                    </td>

                                    <td class="text-end">
                                        Rp
                                        ${formatRupiah(
                                            allocation.amount
                                        )}
                                    </td>

                                </tr>

                            `;

                            }
                        );

                    }


                    const descriptionWrapper =
                        document.getElementById(
                            'paymentDetailDescriptionWrapper'
                        );

                    const description =
                        document.getElementById(
                            'paymentDetailDescription'
                        );


                    if (
                        payment.description
                    ) {

                        description.textContent =
                            payment.description;

                        descriptionWrapper.classList.remove(
                            'd-none'
                        );

                    } else {

                        description.textContent =
                            '';

                        descriptionWrapper.classList.add(
                            'd-none'
                        );

                    }


                    loading.classList.add(
                        'd-none'
                    );

                    content.classList.remove(
                        'd-none'
                    );


                } catch (exception) {

                    loading.classList.add(
                        'd-none'
                    );

                    error.textContent =
                        exception.message;

                    error.classList.remove(
                        'd-none'
                    );

                }

            }


            /*
            |--------------------------------------------------------------------------
            | Klik tombol detail
            |--------------------------------------------------------------------------
            */

            document.addEventListener(
                'click',
                function(event) {

                    const button =
                        event.target.closest(
                            '.btn-payment-detail'
                        );


                    if (!button) {
                        return;
                    }


                    const paymentId =
                        button.dataset.paymentId;


                    if (!paymentId) {
                        return;
                    }


                    loadPaymentDetail(
                        paymentId
                    );

                }
            );


            /*
            |--------------------------------------------------------------------------
            | Reset saat modal ditutup
            |--------------------------------------------------------------------------
            */

            modal.addEventListener(
                'hidden.bs.modal',
                function() {

                    resetModal();

                }
            );

        });

    </script>
@endpush
