{{-- =========================================================
MODAL DETAIL JENIS TAGIHAN
========================================================= --}}

<div
    class="modal fade"
    id="modalShowBillType"
    tabindex="-1"
    aria-labelledby="modalShowBillTypeLabel"
    aria-hidden="true"
>

    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content">

            {{-- =====================================================
            HEADER
            ====================================================== --}}

            <div class="modal-header">

                <div>

                    <h5
                        class="modal-title"
                        id="modalShowBillTypeLabel"
                    >
                        Detail Jenis Tagihan
                    </h5>

                    <small class="text-muted">
                        Informasi jenis tagihan.
                    </small>

                </div>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close"
                ></button>

            </div>


            {{-- =====================================================
            BODY
            ====================================================== --}}

            <div class="modal-body">

                <div class="row g-3">

                    {{-- Organisasi --}}

                    <div class="col-md-6">

                        <label class="form-label text-muted mb-1">
                            Organisasi / Unit
                        </label>

                        <div
                            class="fw-semibold"
                            id="showBillTypeOrganization"
                        >
                            —
                        </div>

                    </div>


                    {{-- Kode --}}

                    <div class="col-md-6">

                        <label class="form-label text-muted mb-1">
                            Kode
                        </label>

                        <div>
                            <span
                                class="badge text-bg-light border"
                                id="showBillTypeCode"
                            >
                                —
                            </span>
                        </div>

                    </div>


                    {{-- Nama --}}

                    <div class="col-md-6">

                        <label class="form-label text-muted mb-1">
                            Nama Jenis Tagihan
                        </label>

                        <div
                            class="fw-semibold"
                            id="showBillTypeName"
                        >
                            —
                        </div>

                    </div>


                    {{-- Status --}}

                    <div class="col-md-6">

                        <label class="form-label text-muted mb-1">
                            Status
                        </label>

                        <div id="showBillTypeStatus">
                            —
                        </div>

                    </div>


                    {{-- Deskripsi --}}

                    <div class="col-12">

                        <label class="form-label text-muted mb-1">
                            Deskripsi
                        </label>

                        <div
                            class="border rounded p-3 bg-light"
                            id="showBillTypeDescription"
                        >
                            —
                        </div>

                    </div>

                </div>

            </div>


            {{-- =====================================================
            FOOTER
            ====================================================== --}}

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
