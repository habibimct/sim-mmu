{{-- =========================================================
MODAL HAPUS JENIS TAGIHAN
========================================================= --}}

<div
    class="modal fade"
    id="modalDeleteBillType"
    tabindex="-1"
    aria-labelledby="modalDeleteBillTypeLabel"
    aria-hidden="true"
>

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            {{-- =====================================================
            HEADER
            ====================================================== --}}

            <div class="modal-header">

                <div>

                    <h5
                        class="modal-title"
                        id="modalDeleteBillTypeLabel"
                    >
                        Hapus Jenis Tagihan
                    </h5>

                    <small class="text-muted">
                        Konfirmasi penghapusan data.
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
            FORM
            ====================================================== --}}

            <form
                method="POST"
                id="formDeleteBillType"
            >

                @csrf

                @method('DELETE')


                <div class="modal-body">

                    <div class="alert alert-warning">

                        <div class="d-flex gap-2">

                            <i class="bi bi-exclamation-triangle-fill"></i>

                            <div>

                                <strong>
                                    Perhatian
                                </strong>

                                <p class="mb-0 mt-1">
                                    Data jenis tagihan yang dihapus
                                    tidak dapat dikembalikan.
                                </p>

                            </div>

                        </div>

                    </div>


                    <p class="mb-2">
                        Anda yakin ingin menghapus jenis tagihan:
                    </p>


                    <div
                        class="border rounded p-3 bg-light"
                    >

                        <div class="mb-1">

                            <span class="text-muted">
                                Organisasi:
                            </span>

                            <strong
                                id="deleteBillTypeOrganization"
                            >
                                —
                            </strong>

                        </div>


                        <div class="mb-1">

                            <span class="text-muted">
                                Kode:
                            </span>

                            <strong
                                id="deleteBillTypeCode"
                            >
                                —
                            </strong>

                        </div>


                        <div>

                            <span class="text-muted">
                                Nama:
                            </span>

                            <strong
                                id="deleteBillTypeName"
                            >
                                —
                            </strong>

                        </div>

                    </div>

                </div>


                {{-- =================================================
                FOOTER
                ================================================== --}}

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
                    >
                        <i class="bi bi-trash me-1"></i>
                        Hapus
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>
