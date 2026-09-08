{{-- Modal Tambah Guru --}}
<div class="modal fade"
    id="modalTambahGuru"
    tabindex="-1"
    aria-labelledby="modalTambahGuruLabel"
    aria-hidden="true">

    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content">

            {{-- Header --}}
            <div class="modal-header">

                <h5 class="modal-title" id="modalTambahGuruLabel">
                    <i class="bi bi-person-plus me-1"></i>
                    Tambah Guru
                </h5>

                <button type="button" class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close">
                </button>

            </div>

            {{-- Form --}}
            <form action="{{ route('admin.teachers.store') }}" method="POST">

                @csrf

                <input type="hidden" name="_form" value="create">

                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">

                    {{-- Organisasi / Unit --}}
                    <div class="mb-3">

                        <label class="form-label">
                            Organisasi / Unit
                            <span class="text-danger">*</span>
                        </label>

                        <div class="border rounded p-3">

                            @php
                                $selectedOrganizations = old('organization_ids', []);
                            @endphp

                            @foreach ($organizations as $organization)

                                <div class="form-check mb-2">

                                    <input type="checkbox"
                                        name="organization_ids[]"
                                        value="{{ $organization->id }}"
                                        id="organization_create_{{ $organization->id }}"
                                        class="form-check-input @error('organization_ids') is-invalid @enderror"
                                        @checked(in_array($organization->id, $selectedOrganizations))>

                                    <label
                                        for="organization_create_{{ $organization->id }}"
                                        class="form-check-label">

                                        {{ $organization->name }}

                                    </label>

                                </div>

                            @endforeach

                        </div>

                        @error('organization_ids')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                        @enderror

                        @error('organization_ids.*')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                    {{-- NIK --}}
                    <div class="mb-3">

                        <label for="nik_create" class="form-label">
                            NIK
                            <span class="text-danger">*</span>
                        </label>

                        <input type="text"
                            name="nik"
                            id="nik_create"
                            value="{{ old('nik') }}"
                            class="form-control @error('nik') is-invalid @enderror"
                            maxlength="16"
                            required>

                        @error('nik')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                    {{-- Nama --}}
                    <div class="mb-3">

                        <label for="name_create" class="form-label">
                            Nama Guru
                            <span class="text-danger">*</span>
                        </label>

                        <input type="text"
                            name="name"
                            id="name_create"
                            value="{{ old('name') }}"
                            class="form-control @error('name') is-invalid @enderror"
                            required>

                        @error('name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                    {{-- Jenis Kelamin --}}
                    <div class="mb-3">

                        <label for="gender_create" class="form-label">
                            Jenis Kelamin
                            <span class="text-danger">*</span>
                        </label>

                        <select name="gender"
                            id="gender_create"
                            class="form-select @error('gender') is-invalid @enderror"
                            required>

                            <option value="">
                                -- Pilih Jenis Kelamin --
                            </option>

                            <option value="male"
                                @selected(old('gender') === 'male')>
                                Laki-laki
                            </option>

                            <option value="female"
                                @selected(old('gender') === 'female')>
                                Perempuan
                            </option>

                        </select>

                        @error('gender')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                    {{-- Tempat Lahir --}}
                    <div class="mb-3">

                        <label for="birth_place_create" class="form-label">
                            Tempat Lahir
                        </label>

                        <input type="text"
                            name="birth_place"
                            id="birth_place_create"
                            value="{{ old('birth_place') }}"
                            class="form-control @error('birth_place') is-invalid @enderror">

                        @error('birth_place')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                    {{-- Tanggal Lahir --}}
                    <div class="mb-3">

                        <label for="birth_date_create" class="form-label">
                            Tanggal Lahir
                        </label>

                        <input type="date"
                            name="birth_date"
                            id="birth_date_create"
                            value="{{ old('birth_date') }}"
                            class="form-control @error('birth_date') is-invalid @enderror">

                        @error('birth_date')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                    {{-- No. HP --}}
                    <div class="mb-3">

                        <label for="phone_create" class="form-label">
                            No. HP
                        </label>

                        <input type="text"
                            name="phone"
                            id="phone_create"
                            value="{{ old('phone') }}"
                            class="form-control @error('phone') is-invalid @enderror">

                        @error('phone')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                    {{-- Email --}}
                    <div class="mb-3">

                        <label for="email_create" class="form-label">
                            Email
                        </label>

                        <input type="email"
                            name="email"
                            id="email_create"
                            value="{{ old('email') }}"
                            class="form-control @error('email') is-invalid @enderror">

                        @error('email')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                    {{-- Status --}}
                    <div class="mb-3">

                        <label for="is_active_create" class="form-label">
                            Status
                            <span class="text-danger">*</span>
                        </label>

                        <select name="is_active"
                            id="is_active_create"
                            class="form-select @error('is_active') is-invalid @enderror">

                            <option value="1"
                                @selected(old('is_active', '1') == '1')}>
                                Aktif
                            </option>

                            <option value="0"
                                @selected(old('is_active') === '0')}>
                                Nonaktif
                            </option>

                        </select>

                        @error('is_active')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                </div>

                {{-- Footer --}}
                <div class="modal-footer">

                    <button type="button"
                        class="btn btn-outline-secondary"
                        data-bs-dismiss="modal">

                        Batal

                    </button>

                    <button type="submit"
                        class="btn btn-primary">

                        <i class="bi bi-save me-1"></i>
                        Simpan Guru

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

