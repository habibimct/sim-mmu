<div class="modal fade"
     id="modalDepositDetail"
     tabindex="-1"
     aria-hidden="true">

    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title">

                    <i class="bi bi-arrow-left-right me-1"></i>

                    Detail Setoran

                </h5>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close">
                </button>

            </div>


            <div class="modal-body">

                <div id="depositDetailLoading"
                     class="text-center py-4">

                    <div class="spinner-border text-primary"
                         role="status">
                    </div>

                    <div class="mt-2 text-muted">
                        Memuat data...
                    </div>

                </div>


                <div id="depositDetailContent"
                     class="d-none">

                    <div class="row g-3">

                        <div class="col-md-6">

                            <div class="text-muted small">
                                Tanggal Setoran
                            </div>

                            <div class="fw-semibold"
                                 id="detailDepositDate">
                                -
                            </div>

                        </div>


                        <div class="col-md-6">

                            <div class="text-muted small">
                                Status
                            </div>

                            <div id="detailDepositStatus">
                                -
                            </div>

                        </div>


                        <div class="col-md-6">

                            <div class="text-muted small">
                                Unit Pengirim
                            </div>

                            <div class="fw-semibold"
                                 id="detailDepositOrganization">
                                -
                            </div>

                        </div>


                        <div class="col-md-6">

                            <div class="text-muted small">
                                Tujuan
                            </div>

                            <div class="fw-semibold"
                                 id="detailDepositTarget">
                                -
                            </div>

                        </div>


                        <div class="col-md-6">

                            <div class="text-muted small">
                                Nominal
                            </div>

                            <div class="fw-bold fs-5"
                                 id="detailDepositAmount">
                                -
                            </div>

                        </div>


                        <div class="col-md-6">

                            <div class="text-muted small">
                                Metode Pembayaran
                            </div>

                            <div class="fw-semibold"
                                 id="detailDepositMethod">
                                -
                            </div>

                        </div>


                        <div class="col-12">

                            <div class="text-muted small">
                                Keterangan
                            </div>

                            <div id="detailDepositDescription">
                                -
                            </div>

                        </div>


                        <div class="col-12"
                             id="detailDepositProofWrapper">

                            <hr>

                            <div class="text-muted small mb-1">
                                Bukti Setoran
                            </div>

                            <a href="#"
                               id="detailDepositProof"
                               target="_blank"
                               class="btn btn-sm btn-outline-primary">

                                <i class="bi bi-image"></i>

                                Lihat Bukti

                            </a>

                        </div>


                        <div class="col-md-6">

                            <div class="text-muted small">
                                Dibuat Oleh
                            </div>

                            <div id="detailDepositCreator">
                                -
                            </div>

                        </div>


                        <div class="col-md-6">

                            <div class="text-muted small">
                                Dikonfirmasi Oleh
                            </div>

                            <div id="detailDepositConfirmer">
                                -
                            </div>

                        </div>


                        <div class="col-md-6">

                            <div class="text-muted small">
                                Waktu Konfirmasi
                            </div>

                            <div id="detailDepositConfirmedAt">
                                -
                            </div>

                        </div>


                        <div class="col-12"
                             id="detailDepositRejectionWrapper">

                            <hr>

                            <div class="text-muted small">
                                Alasan Penolakan
                            </div>

                            <div class="text-danger"
                                 id="detailDepositRejectionReason">
                                -
                            </div>

                        </div>

                    </div>

                </div>

            </div>


            <div class="modal-footer">

                <button type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">

                    Tutup

                </button>

            </div>

        </div>

    </div>

</div>
