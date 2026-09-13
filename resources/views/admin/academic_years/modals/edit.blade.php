{{-- ========================================================= --}}
{{-- MODAL EDIT TAHUN AJARAN --}}
{{-- ========================================================= --}}

@can('settings.manage')

    @foreach ($academicYears as $academicYear)

        @if ($academicYear->is_active)

            <div class="modal fade"
                 id="modalEditTahunAjaran{{ $academicYear->id }}"
                 tabindex="-1"
                 aria-labelledby="modalEditTahunAjaranLabel{{ $academicYear->id }}"
                 aria-hidden="true">

                <div class="modal-dialog modal-dialog-centered">

                    <div class="modal-content">

                        <form action="{{ route('admin.academic-years.update', $academicYear) }}"
                              method="POST">

                            @csrf
                            @method('PUT')

                            {{-- Target modal untuk membuka kembali setelah validasi --}}
                            <input type="hidden"
                                   name="modal_target"
                                   value="modalEditTahunAjaran{{ $academicYear->id }}">


                            {{-- HEADER --}}
                            <div class="modal-header">

                                <h5 class="modal-title"
                                    id="modalEditTahunAjaranLabel{{ $academicYear->id }}">

                                    <i class="bi bi-pencil me-1"></i>
                                    Edit Tahun Ajaran

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
                                           value="{{ old('name', $academicYear->name) }}"
                                           maxlength="20"
                                           required>

                                    @error('name')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                </div>


                                {{-- Tanggal Mulai --}}
                                <div class="mb-3">

                                    <label class="form-label">
                                        Tanggal Mulai
                                        <span class="text-danger">*</span>
                                    </label>

                                    <input type="date"
                                           name="start_date"
                                           class="form-control @error('start_date') is-invalid @enderror"
                                           value="{{ old('start_date', $academicYear->start_date?->format('Y-m-d')) }}"
                                           required>

                                    @error('start_date')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                </div>


                                {{-- Tanggal Selesai --}}
                                <div class="mb-3">

                                    <label class="form-label">
                                        Tanggal Selesai
                                        <span class="text-danger">*</span>
                                    </label>

                                    <input type="date"
                                           name="end_date"
                                           class="form-control @error('end_date') is-invalid @enderror"
                                           value="{{ old('end_date', $academicYear->end_date?->format('Y-m-d')) }}"
                                           required>

                                    @error('end_date')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                </div>


                                {{-- Informasi Status --}}
                                <div class="alert alert-info mb-0">

                                    <i class="bi bi-info-circle me-1"></i>

                                    Status saat ini:
                                    <strong>Terbuka</strong>.

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
                                    Simpan Perubahan

                                </button>

                            </div>

                        </form>

                    </div>

                </div>

            </div>

        @endif

    @endforeach

@endcan
