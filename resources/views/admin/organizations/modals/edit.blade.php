@foreach ($organizations as $organization)

    @php
        $isErrorModal = old('_modal') === 'edit-' . $organization->id;
    @endphp

    <div class="modal fade"
         id="editOrganizationModal{{ $organization->id }}"
         tabindex="-1"
         aria-labelledby="editOrganizationModalLabel{{ $organization->id }}"
         aria-hidden="true">

        <div class="modal-dialog modal-lg modal-dialog-centered">

            <div class="modal-content">

                <div class="modal-header">

                    <h5 class="modal-title"
                        id="editOrganizationModalLabel{{ $organization->id }}">
                        <i class="bi bi-pencil-square"></i>
                        Edit Organisasi
                    </h5>

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close">
                    </button>

                </div>


                <form action="{{ route('admin.organizations.update', $organization) }}"
                      method="POST">

                    @csrf
                    @method('PUT')

                    {{-- Penanda modal --}}
                    <input type="hidden"
                           name="_modal"
                           value="edit-{{ $organization->id }}">


                    <div class="modal-body">

                        <div class="row">

                            {{-- KODE --}}
                            <div class="col-md-6 mb-3">

                                <label for="edit_code_{{ $organization->id }}"
                                       class="form-label">
                                    Kode Organisasi
                                </label>

                                <input type="text"
                                       name="code"
                                       id="edit_code_{{ $organization->id }}"
                                       class="form-control @if ($isErrorModal && $errors->has('code')) is-invalid @endif"
                                       value="{{ $isErrorModal ? old('code', $organization->code) : $organization->code }}"
                                       required>

                                @if ($isErrorModal)
                                    @error('code')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                @endif

                            </div>


                            {{-- NAMA --}}
                            <div class="col-md-6 mb-3">

                                <label for="edit_name_{{ $organization->id }}"
                                       class="form-label">
                                    Nama Organisasi
                                </label>

                                <input type="text"
                                       name="name"
                                       id="edit_name_{{ $organization->id }}"
                                       class="form-control @if ($isErrorModal && $errors->has('name')) is-invalid @endif"
                                       value="{{ $isErrorModal ? old('name', $organization->name) : $organization->name }}"
                                       required>

                                @if ($isErrorModal)
                                    @error('name')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                @endif

                            </div>


                            {{-- JENIS --}}
                            <div class="col-md-6 mb-3">

                                <label for="edit_type_{{ $organization->id }}"
                                       class="form-label">
                                    Jenis Organisasi
                                </label>

                                <select name="type"
                                        id="edit_type_{{ $organization->id }}"
                                        class="form-select @if ($isErrorModal && $errors->has('type')) is-invalid @endif"
                                        required>

                                    <option value="induk"
                                        @selected(
                                            ($isErrorModal
                                                ? old('type', $organization->type)
                                                : $organization->type
                                            ) === 'induk'
                                        )>
                                        INDUK
                                    </option>

                                    <option value="unit"
                                        @selected(
                                            ($isErrorModal
                                                ? old('type', $organization->type)
                                                : $organization->type
                                            ) === 'unit'
                                        )>
                                        Unit
                                    </option>

                                </select>

                                @if ($isErrorModal)
                                    @error('type')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                @endif

                                <div class="form-text">
                                    Parent organisasi ditentukan otomatis
                                    berdasarkan jenis organisasi.
                                </div>

                            </div>


                            {{-- STATUS --}}
                            <div class="col-md-6 mb-3">

                                <label for="edit_is_active_{{ $organization->id }}"
                                       class="form-label">
                                    Status
                                </label>

                                <select name="is_active"
                                        id="edit_is_active_{{ $organization->id }}"
                                        class="form-select @if ($isErrorModal && $errors->has('is_active')) is-invalid @endif"
                                        required>

                                    <option value="1"
                                        @selected(
                                            ($isErrorModal
                                                ? old(
                                                    'is_active',
                                                    $organization->is_active ? '1' : '0'
                                                )
                                                : ($organization->is_active ? '1' : '0')
                                            ) == '1'
                                        )>
                                        Aktif
                                    </option>

                                    <option value="0"
                                        @selected(
                                            ($isErrorModal
                                                ? old(
                                                    'is_active',
                                                    $organization->is_active ? '1' : '0'
                                                )
                                                : ($organization->is_active ? '1' : '0')
                                            ) == '0'
                                        )>
                                        Tidak Aktif
                                    </option>

                                </select>

                                @if ($organization->type === 'induk')
                                    <div class="form-text">
                                        Organisasi Induk harus selalu aktif.
                                    </div>
                                @endif

                                @if ($isErrorModal)
                                    @error('is_active')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                @endif

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
                            Simpan Perubahan
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

@endforeach
