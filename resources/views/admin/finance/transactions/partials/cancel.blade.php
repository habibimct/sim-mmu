{{-- ==========================================================
MODAL BATALKAN TRANSAKSI
========================================================== --}}

<div class="modal fade" id="modalBatalkanTransaksi" tabindex="-1" aria-labelledby="modalBatalkanTransaksiLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title" id="modalBatalkanTransaksiLabel">
                    <i class="bi bi-exclamation-triangle text-danger me-1"></i>
                    Batalkan Transaksi
                </h5>

                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>

            </div>


            <form method="POST" id="formBatalkanTransaksi">

                @csrf

                <div class="modal-body">

                    <p class="mb-3">
                        Anda yakin ingin membatalkan transaksi berikut?
                    </p>


                    {{-- Informasi transaksi --}}

                    <div class="bg-light rounded p-3 mb-3">

                        <div class="row mb-2">

                            <div class="col-5 text-muted">
                                Jenis
                            </div>

                            <div class="col-7 fw-semibold" id="cancelTransactionType">
                                -
                            </div>

                        </div>


                        <div class="row mb-2">

                            <div class="col-5 text-muted">
                                Kategori
                            </div>

                            <div class="col-7 fw-semibold" id="cancelTransactionCategory">
                                -
                            </div>

                        </div>


                        <div class="row">

                            <div class="col-5 text-muted">
                                Jumlah
                            </div>

                            <div class="col-7 fw-bold" id="cancelTransactionAmount">
                                -
                            </div>

                        </div>

                    </div>


                    {{-- Alasan --}}

                    <div>

                        <label for="cancellation_reason" class="form-label">
                            Alasan Pembatalan
                            <span class="text-danger">*</span>
                        </label>

                        <textarea name="cancellation_reason" id="cancellation_reason" class="form-control" rows="4" maxlength="1000"
                            required placeholder="Masukkan alasan pembatalan..."></textarea>

                        <div class="form-text">
                            Alasan pembatalan wajib diisi.
                        </div>

                    </div>

                </div>


                <div class="modal-footer">

                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Tutup
                    </button>

                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-x-circle me-1"></i>
                        Batalkan Transaksi
                    </button>

                </div>

            </form>

        </div>

    </div>
</div>

@push('js')

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const modal = document.getElementById(
                'modalBatalkanTransaksi'
            );

            const form = document.getElementById(
                'formBatalkanTransaksi'
            );

            const type = document.getElementById(
                'cancelTransactionType'
            );

            const category = document.getElementById(
                'cancelTransactionCategory'
            );

            const amount = document.getElementById(
                'cancelTransactionAmount'
            );

            const reason = document.getElementById(
                'cancellation_reason'
            );


            modal.addEventListener(
                'show.bs.modal',
                function(event) {

                    const button =
                        event.relatedTarget;


                    const cancelUrl =
                        button.getAttribute(
                            'data-cancel-url'
                        );


                    const transactionType =
                        button.getAttribute(
                            'data-transaction-type'
                        );


                    const transactionCategory =
                        button.getAttribute(
                            'data-transaction-category'
                        );


                    const transactionAmount =
                        button.getAttribute(
                            'data-transaction-amount'
                        );


                    /*
                    |--------------------------------------------------------------------------
                    | Isi informasi transaksi
                    |--------------------------------------------------------------------------
                    */

                    type.textContent =
                        transactionType;

                    category.textContent =
                        transactionCategory;

                    amount.textContent =
                        'Rp ' + transactionAmount;


                    /*
                    |--------------------------------------------------------------------------
                    | URL pembatalan
                    |--------------------------------------------------------------------------
                    */

                    form.action =
                        cancelUrl;


                    /*
                    |--------------------------------------------------------------------------
                    | Bersihkan alasan
                    |--------------------------------------------------------------------------
                    */

                    reason.value = '';

                }
            );



            form.addEventListener(
                'submit',
                function(event) {

                    console.log(
                        'FORM SUBMIT TERDETEKSI'
                    );

                    console.log(
                        'Method:',
                        form.method
                    );

                    console.log(
                        'Action:',
                        form.action
                    );

                    console.log(
                        'Reason:',
                        reason.value
                    );

                }
            );

        });
    </script>

@endpush
