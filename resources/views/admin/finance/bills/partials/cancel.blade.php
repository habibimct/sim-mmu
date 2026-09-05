{{-- =========================================================
MODAL BATALKAN TAGIHAN
========================================================= --}}

<div
    class="modal fade"
    id="modalCancelStudentBill"
    tabindex="-1"
    aria-labelledby="modalCancelStudentBillLabel"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header">

                <div>

                    <h5
                        class="modal-title"
                        id="modalCancelStudentBillLabel"
                    >
                        Batalkan Tagihan
                    </h5>

                    <small class="text-muted">
                        Tindakan ini akan mengubah status tagihan menjadi dibatalkan.
                    </small>

                </div>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                ></button>

            </div>


            <form
                method="POST"
                id="formCancelStudentBill"
            >

                @csrf


                <div class="modal-body">

                    <div class="alert alert-warning">

                        <i class="bi bi-exclamation-triangle me-1"></i>

                        Tagihan yang dibatalkan tidak dapat digunakan
                        untuk proses pembayaran.

                    </div>


                    {{-- INFORMASI TAGIHAN --}}

                    <dl class="row mb-3">

                        <dt class="col-sm-4">
                            Siswa
                        </dt>

                        <dd
                            class="col-sm-8"
                            id="cancel_bill_student"
                        >
                            —
                        </dd>


                        <dt class="col-sm-4">
                            Jenis
                        </dt>

                        <dd
                            class="col-sm-8"
                            id="cancel_bill_type"
                        >
                            —
                        </dd>


                        <dt class="col-sm-4">
                            Periode
                        </dt>

                        <dd
                            class="col-sm-8"
                            id="cancel_bill_period"
                        >
                            —
                        </dd>


                        <dt class="col-sm-4">
                            Nominal
                        </dt>

                        <dd
                            class="col-sm-8 fw-semibold"
                            id="cancel_bill_amount"
                        >
                            —
                        </dd>

                    </dl>


                    {{-- ALASAN --}}

                    <div>

                        <label
                            for="cancellation_reason"
                            class="form-label"
                        >
                            Alasan Pembatalan
                            <span class="text-danger">*</span>
                        </label>

                        <textarea
                            name="cancellation_reason"
                            id="cancellation_reason"
                            rows="4"
                            class="form-control"
                            maxlength="1000"
                            required
                            placeholder="Tuliskan alasan pembatalan..."
                        ></textarea>

                    </div>

                </div>


                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal"
                    >
                        Batal
                    </button>

                    <button
                        type="submit"
                        class="btn btn-danger"
                        id="btnCancelStudentBill"
                    >
                        <i class="bi bi-x-circle me-1"></i>
                        Batalkan Tagihan
                    </button>

                </div>

            </form>

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
                'modalCancelStudentBill'
            );

        const form =
            document.getElementById(
                'formCancelStudentBill'
            );

        const reason =
            document.getElementById(
                'cancellation_reason'
            );

        if (!modal || !form) {
            return;
        }


        modal.addEventListener(
            'show.bs.modal',
            function (event) {

                const button =
                    event.relatedTarget;


                /*
                |--------------------------------------------------------------------------
                | Action
                |--------------------------------------------------------------------------
                */

                form.action =
                    button.dataset.cancelUrl;


                /*
                |--------------------------------------------------------------------------
                | Informasi tagihan
                |--------------------------------------------------------------------------
                */

                document.getElementById(
                    'cancel_bill_student'
                ).textContent =
                    button.dataset.student || '—';


                document.getElementById(
                    'cancel_bill_type'
                ).textContent =
                    button.dataset.billType || '—';


                document.getElementById(
                    'cancel_bill_period'
                ).textContent =
                    button.dataset.period || '—';


                document.getElementById(
                    'cancel_bill_amount'
                ).textContent =
                    button.dataset.amount || '—';


                /*
                |--------------------------------------------------------------------------
                | Bersihkan alasan
                |--------------------------------------------------------------------------
                */

                reason.value = '';

            }
        );


        /*
        |--------------------------------------------------------------------------
        | Cegah double click
        |--------------------------------------------------------------------------
        */

        form.addEventListener(
            'submit',
            function () {

                const button =
                    document.getElementById(
                        'btnCancelStudentBill'
                    );

                if (!button) {
                    return;
                }

                if (button.disabled) {
                    return;
                }

                button.disabled = true;

                button.innerHTML = `
                    <span
                        class="spinner-border spinner-border-sm me-1"
                        role="status"
                        aria-hidden="true"
                    ></span>
                    Membatalkan...
                `;

            }
        );

    });

</script>

@endpush
