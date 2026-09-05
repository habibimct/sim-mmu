{{-- =========================================================
MODAL TAMBAH JENIS TAGIHAN
========================================================= --}}

<div
    class="modal fade"
    id="modalCreateBillType"
    tabindex="-1"
    aria-labelledby="modalCreateBillTypeLabel"
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
                        id="modalCreateBillTypeLabel"
                    >
                        Tambah Jenis Tagihan
                    </h5>

                    <small class="text-muted">
                        Tambahkan jenis tagihan untuk organisasi/unit.
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
                action="{{ route('admin.finance.bill-types.store') }}"
            >

                @csrf


                <div class="modal-body">

                    {{-- =================================================
                    ORGANISASI
                    ================================================== --}}

                    <div class="mb-3">

                        <label
                            for="organization_id"
                            class="form-label"
                        >
                            Organisasi / Unit
                            <span class="text-danger">*</span>
                        </label>

                        <select
                            name="organization_id"
                            id="organization_id"
                            class="form-select @error('organization_id') is-invalid @enderror"
                            required
                        >

                            <option value="">
                                -- Pilih Organisasi --
                            </option>

                            @foreach ($organizations as $organization)

                                <option
                                    value="{{ $organization->id }}"
                                    @selected(
                                        old('organization_id') == $organization->id
                                    )
                                >
                                    {{ $organization->name }}
                                </option>

                            @endforeach

                        </select>

                        @error('organization_id')

                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>


                    {{-- =================================================
                    KODE
                    ================================================== --}}

                    <div class="mb-3">

                        <label
                            for="code"
                            class="form-label"
                        >
                            Kode
                            <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            name="code"
                            id="code"
                            value="{{ old('code') }}"
                            class="form-control @error('code') is-invalid @enderror"
                            maxlength="50"
                            placeholder="Contoh: SPP"
                            required
                        >

                        <div class="form-text">
                            Kode harus unik dalam organisasi tersebut.
                        </div>

                        @error('code')

                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>


                    {{-- =================================================
                    NAMA
                    ================================================== --}}

                    <div class="mb-3">

                        <label
                            for="name"
                            class="form-label"
                        >
                            Nama Jenis Tagihan
                            <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            name="name"
                            id="name"
                            value="{{ old('name') }}"
                            class="form-control @error('name') is-invalid @enderror"
                            maxlength="100"
                            placeholder="Contoh: SPP Bulanan"
                            required
                        >

                        @error('name')

                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>


                    {{-- =================================================
                    DESKRIPSI
                    ================================================== --}}

                    <div class="mb-3">

                        <label
                            for="description"
                            class="form-label"
                        >
                            Deskripsi
                        </label>

                        <textarea
                            name="description"
                            id="description"
                            rows="3"
                            class="form-control @error('description') is-invalid @enderror"
                            maxlength="1000"
                            placeholder="Keterangan tambahan jika diperlukan..."
                        >{{ old('description') }}</textarea>

                        @error('description')

                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>


                    {{-- =================================================
                    STATUS
                    ================================================== --}}

                    <div class="form-check">

                        <input
                            type="checkbox"
                            name="is_active"
                            value="1"
                            id="is_active"
                            class="form-check-input"
                            @checked(
                                old('is_active', true)
                            )
                        >

                        <label
                            for="is_active"
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
                        Simpan
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>
