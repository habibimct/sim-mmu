<div class="modal fade"
    id="guardianEditModal"
    tabindex="-1"
    aria-labelledby="guardianEditModalLabel"
    aria-hidden="true">

    <div class="modal-dialog modal-lg">
        <form method="POST"
            id="guardianEditForm"
            class="modal-content">

            @csrf
            @method('PUT')

            <div class="modal-header">
                <h5 class="modal-title" id="guardianEditModalLabel">
                    <i class="bi bi-pencil me-1"></i>
                    Edit Wali / Orang Tua
                </h5>

                <button type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close">
                </button>
            </div>

            <div class="modal-body">

                <div class="row">

                    <div class="col-md-6 mb-3">
                        <label for="guardian_edit_organization"
                            class="form-label">
                            Unit
                        </label>

                        <select name="organization_id"
                            id="guardian_edit_organization"
                            class="form-select"
                            required>

                            <option value="">
                                Pilih Unit
                            </option>

                            @foreach ($organizations as $organization)
                                <option value="{{ $organization->id }}">
                                    {{ $organization->name }}
                                </option>
                            @endforeach

                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="guardian_edit_name"
                            class="form-label">
                            Nama Wali
                            <span class="text-danger">*</span>
                        </label>

                        <input type="text"
                            name="name"
                            id="guardian_edit_name"
                            class="form-control"
                            required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="guardian_edit_nik"
                            class="form-label">
                            NIK
                            <span class="text-danger">*</span>
                        </label>

                        <input type="text"
                            name="nik"
                            id="guardian_edit_nik"
                            class="form-control"
                            inputmode="numeric"
                            maxlength="16"
                            required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="guardian_edit_phone"
                            class="form-label">
                            No. HP
                        </label>

                        <input type="text"
                            name="phone"
                            id="guardian_edit_phone"
                            class="form-control">
                    </div>

                </div>

            </div>

            <div class="modal-footer">

                <button type="button"
                    class="btn btn-light"
                    data-bs-dismiss="modal">
                    Batal
                </button>

                <button type="submit"
                    class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i>
                    Simpan Perubahan
                </button>

            </div>

        </form>
    </div>
</div>
