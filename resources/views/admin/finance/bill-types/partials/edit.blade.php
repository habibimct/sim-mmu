{{-- =========================================================
MODAL EDIT JENIS TAGIHAN
========================================================= --}}

<div
    class="modal fade"
    id="modalEditBillType"
    tabindex="-1"
    aria-labelledby="modalEditBillTypeLabel"
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
                        id="modalEditBillTypeLabel"
                    >
                        Edit Jenis Tagihan
                    </h5>

                    <small class="text-muted">
                        Ubah informasi jenis tagihan.
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
                id="formEditBillType"
            >

                @csrf

                @method('PUT')


                <div class="modal-body">

                    {{-- =================================================
                    ORGANISASI
                    ================================================== --}}

                    <div class="mb-3">

                        <label
                            for="edit_organization_id"
                            class="form-label"
                        >
                            Organisasi / Unit
                        </label>

                        <select
                            name="organization_id"
                            id="edit_organization_id"
                            class="form-select"
                            required
                        >

                            <option value="">
                                -- Pilih Organisasi --
                            </option>

                            @foreach ($organizations as $organization)

                                <option
                                    value="{{ $organization->id }}"
                                >
                                    {{ $organization->name }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- =================================================
                    KODE
                    ================================================== --}}

                    <div class="mb-3">

                        <label
                            for="edit_code"
                            class="form-label"
                        >
                            Kode
                            <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            name="code"
                            id="edit_code"
                            class="form-control"
                            maxlength="50"
                            required
                        >

                        <div class="form-text">
                            Kode harus unik dalam organisasi tersebut.
                        </div>

                    </div>


                    {{-- =================================================
                    NAMA
                    ================================================== --}}

                    <div class="mb-3">

                        <label
                            for="edit_name"
                            class="form-label"
                        >
                            Nama Jenis Tagihan
                            <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            name="name"
                            id="edit_name"
                            class="form-control"
                            maxlength="100"
                            required
                        >

                    </div>


                    {{-- =================================================
                    DESKRIPSI
                    ================================================== --}}

                    <div class="mb-3">

                        <label
                            for="edit_description"
                            class="form-label"
                        >
                            Deskripsi
                        </label>

                        <textarea
                            name="description"
                            id="edit_description"
                            rows="3"
                            class="form-control"
                            maxlength="1000"
                        ></textarea>

                    </div>


                    {{-- =================================================
                    STATUS
                    ================================================== --}}

                    <div class="form-check">

                        <input
                            type="checkbox"
                            name="is_active"
                            value="1"
                            id="edit_is_active"
                            class="form-check-input"
                        >

                        <label
                            for="edit_is_active"
                            class="form-check-label"
                        >
                            Aktif
                        </label>

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
                        Batal
                    </button>

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        <i class="bi bi-check-lg me-1"></i>
                        Simpan Perubahan
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>
