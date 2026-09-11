<div
    class="modal fade"
    id="modalPaymentReportDetail"
    tabindex="-1"
    aria-hidden="true"
>

    <div class="modal-dialog modal-xl modal-dialog-centered">

        <div class="modal-content">


            <div class="modal-header">

                <h5 class="modal-title">

                    <i class="bi bi-credit-card me-1"></i>

                    Detail Pembayaran

                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                ></button>

            </div>


            <div class="modal-body">


                {{-- LOADING --}}

                <div
                    id="paymentReportDetailLoading"
                    class="text-center py-5"
                >

                    <div
                        class="spinner-border text-primary"
                        role="status"
                    ></div>

                    <div class="mt-2 text-muted">
                        Memuat detail pembayaran...
                    </div>

                </div>


                {{-- CONTENT --}}

                <div
                    id="paymentReportDetailContent"
                    class="d-none"
                >

                    <div class="row mb-4">


                        <div class="col-md-4">

                            <small class="text-muted">
                                Nomor Pembayaran
                            </small>

                            <div
                                id="reportPaymentNumber"
                                class="fw-bold"
                            >
                                -
                            </div>

                        </div>


                        <div class="col-md-4">

                            <small class="text-muted">
                                Tanggal
                            </small>

                            <div id="reportPaymentDate">
                                -
                            </div>

                        </div>


                        <div class="col-md-4">

                            <small class="text-muted">
                                Unit
                            </small>

                            <div id="reportPaymentOrganization">
                                -
                            </div>

                        </div>

                    </div>


                    <div class="row mb-4">


                        <div class="col-md-4">

                            <small class="text-muted">
                                Nominal
                            </small>

                            <div
                                id="reportPaymentAmount"
                                class="fw-bold text-primary"
                            >
                                -
                            </div>

                        </div>


                        <div class="col-md-4">

                            <small class="text-muted">
                                Metode
                            </small>

                            <div id="reportPaymentMethod">
                                -
                            </div>

                        </div>


                        <div class="col-md-4">

                            <small class="text-muted">
                                Status
                            </small>

                            <div id="reportPaymentStatus">
                                -
                            </div>

                        </div>

                    </div>


                    <hr>


                    <h6 class="mb-3">
                        Alokasi Pembayaran
                    </h6>


                    <div class="table-responsive">

                        <table class="table table-bordered align-middle">

                            <thead>

                                <tr>

                                    <th>No.</th>

                                    <th>Siswa</th>

                                    <th>NIS</th>

                                    <th>Kelas</th>

                                    <th>Jenis Tagihan</th>

                                    <th>Periode</th>

                                    <th class="text-end">
                                        Nominal
                                    </th>

                                </tr>

                            </thead>

                            <tbody id="reportPaymentAllocations">

                            </tbody>

                            <tfoot>

                                <tr>

                                    <th
                                        colspan="6"
                                        class="text-end"
                                    >
                                        Total
                                    </th>

                                    <th
                                        id="reportPaymentAllocationTotal"
                                        class="text-end"
                                    >
                                        Rp 0
                                    </th>

                                </tr>

                            </tfoot>

                        </table>

                    </div>


                    <hr>


                    <div class="row">


                        <div class="col-md-4">

                            <small class="text-muted">
                                Dicatat Oleh
                            </small>

                            <div id="reportPaymentCreator">
                                -
                            </div>

                        </div>


                        <div class="col-md-4">

                            <small class="text-muted">
                                Dikonfirmasi Oleh
                            </small>

                            <div id="reportPaymentConfirmer">
                                -
                            </div>

                        </div>


                        <div class="col-md-4">

                            <small class="text-muted">
                                Waktu Konfirmasi
                            </small>

                            <div id="reportPaymentConfirmedAt">
                                -
                            </div>

                        </div>

                    </div>


                    <div class="mt-3">

                        <small class="text-muted">
                            Keterangan
                        </small>

                        <div
                            id="reportPaymentDescription"
                            class="border rounded p-2 mt-1"
                        >
                            -
                        </div>

                    </div>

                </div>


                {{-- ERROR --}}

                <div
                    id="paymentReportDetailError"
                    class="alert alert-danger d-none"
                >
                </div>

            </div>


            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-secondary"
                    data-bs-dismiss="modal"
                >

                    Tutup

                </button>

            </div>

        </div>

    </div>

</div>



@push('js')

<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {

        const modal =
            document.getElementById(
                'modalPaymentReportDetail'
            );

        if (!modal) {
            return;
        }


        modal.addEventListener(
            'show.bs.modal',
            function (event) {

                const button =
                    event.relatedTarget;

                const paymentId =
                    button?.dataset.paymentId;

                if (!paymentId) {
                    return;
                }


                const loading =
                    document.getElementById(
                        'paymentReportDetailLoading'
                    );

                const content =
                    document.getElementById(
                        'paymentReportDetailContent'
                    );

                const error =
                    document.getElementById(
                        'paymentReportDetailError'
                    );


                loading.classList.remove(
                    'd-none'
                );

                content.classList.add(
                    'd-none'
                );

                error.classList.add(
                    'd-none'
                );

                error.textContent = '';


                fetch(
                    `{{ url('admin/laporan/pembayaran') }}/${paymentId}/detail`,
                    {
                        headers: {
                            'Accept':
                                'application/json',

                            'X-Requested-With':
                                'XMLHttpRequest'
                        }
                    }
                )
                .then(
                    response => {

                        if (!response.ok) {

                            throw new Error(
                                'Gagal mengambil detail pembayaran.'
                            );

                        }

                        return response.json();

                    }
                )
                .then(
                    data => {

                        document.getElementById(
                            'reportPaymentNumber'
                        ).textContent =
                            data.payment_number ?? '-';


                        document.getElementById(
                            'reportPaymentDate'
                        ).textContent =
                            data.payment_date ?? '-';


                        document.getElementById(
                            'reportPaymentOrganization'
                        ).textContent =
                            data.organization ?? '-';


                        document.getElementById(
                            'reportPaymentAmount'
                        ).textContent =
                            formatRupiah(
                                data.amount
                            );


                        document.getElementById(
                            'reportPaymentMethod'
                        ).textContent =
                            formatPaymentMethod(
                                data.payment_method
                            );


                        document.getElementById(
                            'reportPaymentStatus'
                        ).innerHTML =
                            formatPaymentStatus(
                                data.status
                            );


                        document.getElementById(
                            'reportPaymentCreator'
                        ).textContent =
                            data.creator ?? '-';


                        document.getElementById(
                            'reportPaymentConfirmer'
                        ).textContent =
                            data.confirmer ?? '-';


                        document.getElementById(
                            'reportPaymentConfirmedAt'
                        ).textContent =
                            data.confirmed_at ?? '-';


                        document.getElementById(
                            'reportPaymentDescription'
                        ).textContent =
                            data.description ?? '-';


                        const tbody =
                            document.getElementById(
                                'reportPaymentAllocations'
                            );

                        tbody.innerHTML = '';


                        let total = 0;


                        if (
                            data.allocations
                            &&
                            data.allocations.length
                        ) {

                            data.allocations.forEach(
                                function (
                                    allocation,
                                    index
                                ) {

                                    total +=
                                        Number(
                                            allocation.amount
                                        );


                                    const row =
                                        document.createElement(
                                            'tr'
                                        );


                                    row.innerHTML = `

                                        <td>
                                            ${index + 1}
                                        </td>

                                        <td>
                                            ${escapeHtml(
                                                allocation.student_name ?? '-'
                                            )}
                                        </td>

                                        <td>
                                            ${escapeHtml(
                                                allocation.nis ?? '-'
                                            )}
                                        </td>

                                        <td>
                                            ${escapeHtml(
                                                allocation.class ?? '-'
                                            )}
                                        </td>

                                        <td>
                                            ${escapeHtml(
                                                allocation.bill_type ?? '-'
                                            )}
                                        </td>

                                        <td>
                                            ${escapeHtml(
                                                allocation.period ?? '-'
                                            )}
                                        </td>

                                        <td class="text-end text-nowrap">
                                            ${formatRupiah(
                                                allocation.amount
                                            )}
                                        </td>

                                    `;


                                    tbody.appendChild(
                                        row
                                    );

                                }
                            );

                        } else {

                            tbody.innerHTML = `

                                <tr>

                                    <td
                                        colspan="7"
                                        class="text-center text-muted"
                                    >
                                        Tidak ada alokasi pembayaran.

                                    </td>

                                </tr>

                            `;

                        }


                        document.getElementById(
                            'reportPaymentAllocationTotal'
                        ).textContent =
                            formatRupiah(total);


                        loading.classList.add(
                            'd-none'
                        );

                        content.classList.remove(
                            'd-none'
                        );

                    }
                )
                .catch(
                    err => {

                        loading.classList.add(
                            'd-none'
                        );

                        error.textContent =
                            err.message;

                        error.classList.remove(
                            'd-none'
                        );

                    }
                );

            }
        );


        function formatRupiah(
            amount
        ) {

            return 'Rp ' +
                Number(amount || 0)
                    .toLocaleString(
                        'id-ID'
                    );

        }


        function formatPaymentMethod(
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


        function formatPaymentStatus(
            status
        ) {

            switch (status) {

                case 'pending':

                    return `
                        <span class="badge bg-warning text-dark">
                            Menunggu
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
                            ${escapeHtml(status ?? '-')}
                        </span>
                    `;

            }

        }


        function escapeHtml(
            value
        ) {

            const div =
                document.createElement(
                    'div'
                );

            div.textContent =
                value ?? '';

            return div.innerHTML;

        }

    }
);

</script>

@endpush
