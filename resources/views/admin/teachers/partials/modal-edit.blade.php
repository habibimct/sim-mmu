{{-- Modal Edit Guru --}}
<div class="modal fade"
    id="modalEditGuru{{ $teacher->id }}"
    tabindex="-1"
    aria-labelledby="modalEditGuruLabel{{ $teacher->id }}"
    aria-hidden="true">

    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">

        <div class="modal-content">

            {{-- Header --}}
            <div class="modal-header">

                <h5 class="modal-title"
                    id="modalEditGuruLabel{{ $teacher->id }}">

                    <i class="bi bi-pencil-square me-1"></i>
                    Edit Guru

                </h5>

                <button type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close">
                </button>

            </div>

            {{-- Form --}}
            <form action="{{ route('admin.teachers.update', $teacher) }}"
                method="POST">

                @csrf
                @method('PUT')

                {{-- Penanda form edit --}}
                <input type="hidden" name="_form" value="edit">
                <input type="hidden" name="_user_id" value="{{ $teacher->id }}">

                <div class="modal-body">

                    {{-- Organisasi / Unit --}}
                    <div class="mb-3">

                        <label class="form-label">
                            Organisasi / Unit
                        </label>

                        @php
                            $selectedOrganizations = old(
                                'organization_ids',
                                $teacher->organizations->pluck('id')->all()
                            );
                        @endphp

                        <div class="border rounded p-3">

                            @foreach ($organizations as $organization)

                                <div class="form-check mb-2">

                                    <input type="checkbox"
                                        name="organization_ids[]"
                                        value="{{ $organization->id }}"
                                        id="organization_edit_{{ $teacher->id }}_{{ $organization->id }}"
                                        class="form-check-input @error('organization_ids') is-invalid @enderror"
                                        @checked(in_array($organization->id, $selectedOrganizations))>

                                    <label
                                        for="organization_edit_{{ $teacher->id }}_{{ $organization->id }}"
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

                        <label for="nik_edit_{{ $teacher->id }}"
                            class="form-label">

                            NIK

                        </label>

                        <input type="text"
                            name="nik"
                            id="nik_edit_{{ $teacher->id }}"
                            value="{{ old('nik', $teacher->nik) }}"
                            class="form-control @error('nik') is-invalid @enderror">

                        @error('nik')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    {{-- Nama --}}
                    <div class="mb-3">

                        <label for="name_edit_{{ $teacher->id }}"
                            class="form-label">

                            Nama Guru

                        </label>

                        <input type="text"
                            name="name"
                            id="name_edit_{{ $teacher->id }}"
                            value="{{ old('name', $teacher->name) }}"
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

                        <label for="gender_edit_{{ $teacher->id }}"
                            class="form-label">

                            Jenis Kelamin

                        </label>

                        <select name="gender"
                            id="gender_edit_{{ $teacher->id }}"
                            class="form-select @error('gender') is-invalid @enderror"
                            required>

                            <option value="">
                                -- Pilih Jenis Kelamin --
                            </option>

                            <option value="male"
                                @selected(old('gender', $teacher->gender) === 'male')>
                                Laki-laki
                            </option>

                            <option value="female"
                                @selected(old('gender', $teacher->gender) === 'female')>
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

                        <label for="birth_place_edit_{{ $teacher->id }}"
                            class="form-label">

                            Tempat Lahir

                        </label>

                        <input type="text"
                            name="birth_place"
                            id="birth_place_edit_{{ $teacher->id }}"
                            value="{{ old('birth_place', $teacher->birth_place) }}"
                            class="form-control @error('birth_place') is-invalid @enderror">

                        @error('birth_place')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    {{-- Tanggal Lahir --}}
                    <div class="mb-3">

                        <label for="birth_date_edit_{{ $teacher->id }}"
                            class="form-label">

                            Tanggal Lahir

                        </label>

                        <input type="date"
                            name="birth_date"
                            id="birth_date_edit_{{ $teacher->id }}"
                            value="{{ old(
                                'birth_date',
                                optional($teacher->birth_date)->format('Y-m-d')
                            ) }}"
                            class="form-control @error('birth_date') is-invalid @enderror">

                        @error('birth_date')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    {{-- No. HP --}}
                    <div class="mb-3">

                        <label for="phone_edit_{{ $teacher->id }}"
                            class="form-label">

                            No. HP

                        </label>

                        <input type="text"
                            name="phone"
                            id="phone_edit_{{ $teacher->id }}"
                            value="{{ old('phone', $teacher->phone) }}"
                            class="form-control @error('phone') is-invalid @enderror">

                        @error('phone')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    {{-- Email --}}
                    <div class="mb-3">

                        <label for="email_edit_{{ $teacher->id }}"
                            class="form-label">

                            Email

                        </label>

                        <input type="email"
                            name="email"
                            id="email_edit_{{ $teacher->id }}"
                            value="{{ old('email', $teacher->email) }}"
                            class="form-control @error('email') is-invalid @enderror">

                        @error('email')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    {{-- Status --}}
                    <div class="mb-3">

                        <label for="is_active_edit_{{ $teacher->id }}"
                            class="form-label">

                            Status

                        </label>

                        <select name="is_active"
                            id="is_active_edit_{{ $teacher->id }}"
                            class="form-select @error('is_active') is-invalid @enderror">

                            <option value="1"
                                @selected(old('is_active', $teacher->is_active) == '1')>
                                Aktif
                            </option>

                            <option value="0"
                                @selected(old('is_active', $teacher->is_active) == '0')>
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
                        Simpan Perubahan

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>
