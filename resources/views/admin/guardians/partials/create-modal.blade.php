<div class="modal fade" id="guardianCreateModal" tabindex="-1" aria-labelledby="guardianCreateModalLabel"
    aria-hidden="true">

    <div class="modal-dialog modal-lg">

        <form method="POST" action="{{ route('admin.guardians.store') }}" class="modal-content">

            @csrf

            <div class="modal-header">

                <h5 class="modal-title" id="guardianCreateModalLabel">

                    <i class="bi bi-person-plus me-1"></i>
                    Tambah Wali / Orang Tua

                </h5>

                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

            </div>

            <div class="modal-body">

                <div class="row">

                    {{-- Unit --}}
                    <div class="col-md-6 mb-3">

                        <label for="guardian_organization_create" class="form-label">
                            Unit
                        </label>

                        <select name="organization_id" id="guardian_organization_create" class="form-select" required>

                            <option value="">
                                Pilih Unit
                            </option>

                            @foreach ($organizations as $organization)
                                <option value="{{ $organization->id }}" @selected(old('organization_id') == $organization->id)>
                                    {{ $organization->name }}
                                </option>
                            @endforeach

                        </select>

                    </div>

                    {{-- Nama --}}
                    <div class="col-md-6 mb-3">

                        <label for="guardian_name" class="form-label">
                            Nama Wali
                        </label>

                        <input type="text" name="name" id="guardian_name" class="form-control"
                            value="{{ old('name') }}" required>

                    </div>

                    {{-- NIK --}}
                    <div class="col-md-6 mb-3">

                        <label for="guardian_nik" class="form-label">
                            NIK <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="nik" id="guardian_nik" class="form-control"
                            value="{{ old('nik') }}" inputmode="numeric" maxlength="16" required>

                    </div>

                    {{-- No HP --}}
                    <div class="col-md-6 mb-3">

                        <label for="guardian_phone" class="form-label">
                            No. HP
                        </label>

                        <input type="text" name="phone" id="guardian_phone" class="form-control"
                            value="{{ old('phone') }}">

                    </div>

                </div>

            </div>

            <div class="modal-footer">

                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                    Batal
                </button>

                <button type="submit" class="btn btn-primary">

                    <i class="bi bi-check-lg me-1"></i>
                    Simpan

                </button>

            </div>

        </form>

    </div>

</div>
