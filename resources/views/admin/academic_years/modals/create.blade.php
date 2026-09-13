{{-- ========================================================= --}}
{{-- MODAL TAMBAH TAHUN AJARAN --}}
{{-- ========================================================= --}}

@can('academic_years.manage')

    <div class="modal fade"
         id="modalTambahTahunAjaran"
         tabindex="-1"
         aria-labelledby="modalTambahTahunAjaranLabel"
         aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered">

            <div class="modal-content">

                <form action="{{ route('admin.academic-years.store') }}"
                      method="POST">

                    @csrf

                    {{-- Target modal untuk membuka kembali setelah validasi --}}
                    <input type="hidden"
                           name="modal_target"
                           value="modalTambahTahunAjaran">


                    {{-- HEADER --}}
                    <div class="modal-header">

                        <h5 class="modal-title"
                            id="modalTambahTahunAjaranLabel">

                            <i class="bi bi-plus-lg me-1"></i>
                            Tambah Tahun Ajaran

                        </h5>

                        <button type="button"
                                class="btn-close"
                                data-bs-dismiss="modal"
                                aria-label="Close">
                        </button>

                    </div>


                    {{-- BODY --}}
                    <div class="modal-body">

                        {{-- Tahun Ajaran --}}
                        <div class="mb-3">

                            <label class="form-label">
                                Tahun Ajaran
                                <span class="text-danger">*</span>
                            </label>

                            <input type="text"
                                   name="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name') }}"
                                   placeholder="Contoh: 2026/2027"
                                   maxlength="20"
                                   required>

                            @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        {{-- Mulai --}}
                        <div class="mb-3">

                            <label class="form-label">
                                Tanggal Mulai
                                <span class="text-danger">*</span>
                            </label>

                            <input type="date"
                                   name="start_date"
                                   class="form-control @error('start_date') is-invalid @enderror"
                                   value="{{ old('start_date') }}"
                                   required>

                            @error('start_date')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        {{-- Selesai --}}
                        <div class="mb-3">

                            <label class="form-label">
                                Tanggal Selesai
                                <span class="text-danger">*</span>
                            </label>

                            <input type="date"
                                   name="end_date"
                                   class="form-control @error('end_date') is-invalid @enderror"
                                   value="{{ old('end_date') }}"
                                   required>

                            @error('end_date')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        {{-- Status --}}
                        <div class="mb-3">

                            <label class="form-label">
                                Status
                            </label>

                            <select name="is_active"
                                    class="form-select @error('is_active') is-invalid @enderror">

                                <option value="1"
                                    @selected(old('is_active', '1') == '1')>
                                    Terbuka
                                </option>

                                <option value="0"
                                    @selected(old('is_active') === '0')>
                                    Ditutup
                                </option>

                            </select>

                            @error('is_active')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                            <small class="text-muted">
                                Terbuka berarti periode dapat dikelola.
                            </small>

                        </div>

                    </div>


                    {{-- FOOTER --}}
                    <div class="modal-footer">

                        <button type="button"
                                class="btn btn-secondary"
                                data-bs-dismiss="modal">

                            Batal

                        </button>

                        <button type="submit"
                                class="btn btn-primary">

                            <i class="bi bi-save me-1"></i>
                            Simpan

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

@endcan
