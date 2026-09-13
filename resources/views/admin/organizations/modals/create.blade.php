<div class="modal fade"
     id="createOrganizationModal"
     tabindex="-1"
     aria-labelledby="createOrganizationModalLabel"
     aria-hidden="true">

    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title"
                    id="createOrganizationModalLabel">
                    <i class="bi bi-building-add"></i>
                    Tambah Organisasi
                </h5>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close">
                </button>

            </div>


            <form action="{{ route('admin.organizations.store') }}"
                  method="POST">

                @csrf

                {{-- Penanda modal --}}
                <input type="hidden"
                       name="_modal"
                       value="create">


                <div class="modal-body">

                    <div class="row">

                        {{-- KODE --}}
                        <div class="col-md-6 mb-3">

                            <label for="create_code"
                                   class="form-label">
                                Kode Organisasi
                            </label>

                            <input type="text"
                                   name="code"
                                   id="create_code"
                                   class="form-control @error('code') is-invalid @enderror"
                                   value="{{ old('code') }}"
                                   placeholder="Contoh: MI"
                                   required>

                            @error('code')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        {{-- NAMA --}}
                        <div class="col-md-6 mb-3">

                            <label for="create_name"
                                   class="form-label">
                                Nama Organisasi
                            </label>

                            <input type="text"
                                   name="name"
                                   id="create_name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name') }}"
                                   placeholder="Contoh: Madrasah Ibtidaiyah"
                                   required>

                            @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        {{-- JENIS --}}
                        <div class="col-md-6 mb-3">

                            <label for="create_type"
                                   class="form-label">
                                Jenis Organisasi
                            </label>

                            <select name="type"
                                    id="create_type"
                                    class="form-select @error('type') is-invalid @enderror"
                                    required>

                                <option value="">
                                    -- Pilih Jenis --
                                </option>

                                <option value="induk"
                                    @selected(old('type') === 'induk')>
                                    INDUK
                                </option>

                                <option value="unit"
                                    @selected(old('type') === 'unit')>
                                    Unit
                                </option>

                            </select>

                            @error('type')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                            <div class="form-text">
                                Jika jenis Unit, parent organisasi
                                otomatis ditetapkan ke Induk.
                            </div>

                        </div>


                        {{-- STATUS --}}
                        <div class="col-md-6 mb-3">

                            <label for="create_is_active"
                                   class="form-label">
                                Status
                            </label>

                            <select name="is_active"
                                    id="create_is_active"
                                    class="form-select @error('is_active') is-invalid @enderror"
                                    required>

                                <option value="1"
                                    @selected(old('is_active', '1') == '1')>
                                    Aktif
                                </option>

                                <option value="0"
                                    @selected(old('is_active') === '0')>
                                    Tidak Aktif
                                </option>

                            </select>

                            @error('is_active')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>

                    </div>

                </div>


                <div class="modal-footer">

                    <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">
                        Batal
                    </button>

                    <button type="submit"
                            class="btn btn-primary">
                        <i class="bi bi-save"></i>
                        Simpan
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>
