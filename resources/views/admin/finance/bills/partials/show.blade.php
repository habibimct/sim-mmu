{{-- =========================================================
MODAL DETAIL TAGIHAN SISWA
========================================================= --}}

<div class="modal fade" id="modalShowStudentBill" tabindex="-1" aria-labelledby="modalShowStudentBillLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content">

            {{-- HEADER --}}

            <div class="modal-header">

                <div>
                    <h5 class="modal-title" id="modalShowStudentBillLabel">
                        Detail Tagihan Siswa
                    </h5>

                    <small class="text-muted">
                        Informasi lengkap tagihan siswa.
                    </small>
                </div>

                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

            </div>


            {{-- BODY --}}

            <div class="modal-body">

                <div class="row g-3">

                    {{-- Siswa --}}

                    <div class="col-md-6">

                        <div class="text-muted small">
                            Siswa
                        </div>

                        <div class="fw-semibold" id="show_bill_student">
                            —
                        </div>

                    </div>


                    {{-- NIS --}}

                    <div class="col-md-6">

                        <div class="text-muted small">
                            NIS
                        </div>

                        <div class="fw-semibold" id="show_bill_nis">
                            —
                        </div>

                    </div>


                    {{-- Organisasi --}}

                    <div class="col-md-6">

                        <div class="text-muted small">
                            Organisasi / Unit
                        </div>

                        <div class="fw-semibold" id="show_bill_organization">
                            —
                        </div>

                    </div>


                    {{-- Tahun Akademik --}}

                    <div class="col-md-6">

                        <div class="text-muted small">
                            Tahun Akademik
                        </div>

                        <div class="fw-semibold" id="show_bill_academic_year">
                            —
                        </div>

                    </div>


                    {{-- Kelas --}}

                    <div class="col-md-6">

                        <div class="text-muted small">
                            Kelas
                        </div>

                        <div class="fw-semibold" id="show_bill_class">
                            —
                        </div>

                    </div>


                    {{-- Jenis Tagihan --}}

                    <div class="col-md-6">

                        <div class="text-muted small">
                            Jenis Tagihan
                        </div>

                        <div class="fw-semibold" id="show_bill_type">
                            —
                        </div>

                    </div>


                    {{-- Periode --}}

                    <div class="col-md-6">

                        <div class="text-muted small">
                            Periode
                        </div>

                        <div class="fw-semibold" id="show_bill_period">
                            —
                        </div>

                    </div>


                    {{-- Jatuh Tempo --}}

                    <div class="col-md-6">

                        <div class="text-muted small">
                            Jatuh Tempo
                        </div>

                        <div class="fw-semibold" id="show_bill_due_date">
                            —
                        </div>

                    </div>


                    {{-- Nominal --}}

                    <div class="col-md-6">

                        <div class="text-muted small">
                            Nominal Tagihan
                        </div>

                        <div class="fw-bold fs-5" id="show_bill_amount">
                            —
                        </div>

                    </div>


                    {{-- Status --}}

                    <div class="col-md-6">

                        <div class="text-muted small">
                            Status
                        </div>

                        <div id="show_bill_status">
                            —
                        </div>

                    </div>


                    {{-- Keterangan --}}

                    <div class="col-12">

                        <div class="text-muted small">
                            Keterangan
                        </div>

                        <div id="show_bill_description" class="border rounded p-3 bg-light">
                            —
                        </div>

                    </div>

                    {{-- Pembatalan --}}
                    <div id="show_bill_cancellation" class="col-12 d-none">
                        <div class="border border-danger rounded p-3 bg-light">

                            <div class="fw-semibold text-danger mb-3">
                                <i class="bi bi-x-circle me-1"></i>
                                Informasi Pembatalan
                            </div>

                            <div class="row g-3">

                                <div class="col-md-4">

                                    <div class="text-muted small">
                                        Alasan Pembatalan
                                    </div>

                                    <div id="show_bill_cancellation_reason" class="fw-semibold">
                                        —
                                    </div>

                                </div>

                                <div class="col-md-4">

                                    <div class="text-muted small">
                                        Dibatalkan Oleh
                                    </div>

                                    <div id="show_bill_cancelled_by" class="fw-semibold">
                                        —
                                    </div>

                                </div>

                                <div class="col-md-4">

                                    <div class="text-muted small">
                                        Tanggal Pembatalan
                                    </div>

                                    <div id="show_bill_cancelled_at" class="fw-semibold">
                                        —
                                    </div>

                                </div>

                            </div>

                        </div>
                    </div>

                </div>

            </div>


            {{-- FOOTER --}}

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
        document.addEventListener(
            'DOMContentLoaded',
            function() {

                const modal =
                    document.getElementById(
                        'modalShowStudentBill'
                    );

                if (!modal) {
                    return;
                }


                modal.addEventListener(
                    'show.bs.modal',
                    function(event) {

                        const button =
                            event.relatedTarget;


                        /*
                        |--------------------------------------------------------------------------
                        | Informasi siswa
                        |--------------------------------------------------------------------------
                        */

                        document.getElementById(
                                'show_bill_student'
                            ).textContent =
                            button.dataset.student || '—';


                        document.getElementById(
                                'show_bill_nis'
                            ).textContent =
                            button.dataset.nis || '—';


                        /*
                        |--------------------------------------------------------------------------
                        | Informasi akademik
                        |--------------------------------------------------------------------------
                        */

                        document.getElementById(
                                'show_bill_organization'
                            ).textContent =
                            button.dataset.organization || '—';


                        document.getElementById(
                                'show_bill_academic_year'
                            ).textContent =
                            button.dataset.academicYear || '—';


                        document.getElementById(
                                'show_bill_class'
                            ).textContent =
                            button.dataset.schoolClass || '—';


                        /*
                        |--------------------------------------------------------------------------
                        | Informasi tagihan
                        |--------------------------------------------------------------------------
                        */

                        document.getElementById(
                                'show_bill_type'
                            ).textContent =
                            button.dataset.billType || '—';


                        document.getElementById(
                                'show_bill_period'
                            ).textContent =
                            button.dataset.period || '—';


                        document.getElementById(
                                'show_bill_due_date'
                            ).textContent =
                            button.dataset.dueDate || '—';


                        document.getElementById(
                                'show_bill_amount'
                            ).textContent =
                            button.dataset.amount || '—';


                        /*
                        |--------------------------------------------------------------------------
                        | Status
                        |--------------------------------------------------------------------------
                        */

                        const status =
                            button.dataset.status || '';


                        const statusElement =
                            document.getElementById(
                                'show_bill_status'
                            );


                        let statusHtml = '—';


                        if (status === 'unpaid') {

                            statusHtml =
                                '<span class="badge text-bg-warning">' +
                                'Belum Bayar' +
                                '</span>';

                        } else if (status === 'partial') {

                            statusHtml =
                                '<span class="badge text-bg-info">' +
                                'Sebagian' +
                                '</span>';

                        } else if (status === 'paid') {

                            statusHtml =
                                '<span class="badge text-bg-success">' +
                                'Lunas' +
                                '</span>';

                        } else if (status === 'cancelled') {

                            statusHtml =
                                '<span class="badge text-bg-secondary">' +
                                'Dibatalkan' +
                                '</span>';

                        } else if (status !== '') {

                            statusHtml =
                                '<span class="badge text-bg-light">' +
                                status +
                                '</span>';

                        }


                        statusElement.innerHTML =
                            statusHtml;



                        /*
                        |--------------------------------------------------------------------------
                        | Pembatalan
                        |--------------------------------------------------------------------------
                        */

                        const cancellationBox =
                            document.getElementById(
                                'show_bill_cancellation'
                            );

                        const cancellationReason =
                            document.getElementById(
                                'show_bill_cancellation_reason'
                            );

                        const cancelledBy =
                            document.getElementById(
                                'show_bill_cancelled_by'
                            );

                        const cancelledAt =
                            document.getElementById(
                                'show_bill_cancelled_at'
                            );


                        const cancellationReasonValue =
                            button.dataset.cancellationReason || '';

                        const cancelledByValue =
                            button.dataset.cancelledBy || '';

                        const cancelledAtValue =
                            button.dataset.cancelledAt || '';


                        if (
                            status === 'cancelled'
                        ) {

                            cancellationBox.classList.remove(
                                'd-none'
                            );

                            cancellationReason.textContent =
                                cancellationReasonValue || '—';

                            cancelledBy.textContent =
                                cancelledByValue || '—';

                            cancelledAt.textContent =
                                cancelledAtValue || '—';

                        } else {

                            cancellationBox.classList.add(
                                'd-none'
                            );

                        }


                        /*
                        |--------------------------------------------------------------------------
                        | Keterangan
                        |--------------------------------------------------------------------------
                        */

                        document.getElementById(
                                'show_bill_description'
                            ).textContent =
                            button.dataset.description || '—';

                    }
                );

            });
    </script>
@endpush
