{{-- =================================================
    MODAL TAMBAH USER
================================================= --}}

<div
    class="modal fade"
    id="modalTambahUser"
    tabindex="-1"
    aria-labelledby="modalTambahUserLabel"
    aria-hidden="true">

    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content">

            <form
                method="POST"
                action="{{ route('admin.users.store') }}">

                @csrf

                <input
                    type="hidden"
                    name="_form"
                    value="create">


                {{-- HEADER --}}
                <div class="modal-header">

                    <h5
                        class="modal-title"
                        id="modalTambahUserLabel">

                        <i class="bi bi-person-plus me-2"></i>
                        Tambah User

                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                    </button>

                </div>


                {{-- BODY --}}
                <div class="modal-body">

                    {{-- Nama --}}
                    <div class="mb-3">

                        <label class="form-label">
                            Nama
                        </label>

                        <input
                            type="text"
                            name="name"
                            value="{{ old('name') }}"
                            class="form-control @error('name') is-invalid @enderror"
                            required>

                        @error('name')

                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>


                    {{-- Email --}}
                    <div class="mb-3">

                        <label class="form-label">
                            Email
                        </label>

                        <input
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            class="form-control @error('email') is-invalid @enderror"
                            required>

                        @error('email')

                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>


                    {{-- Password --}}
                    <div class="row">

                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Password
                            </label>

                            <input
                                type="password"
                                name="password"
                                class="form-control @error('password') is-invalid @enderror"
                                required>

                            @error('password')

                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>

                            @enderror

                            <small class="text-muted">
                                Minimal 8 karakter.
                            </small>

                        </div>


                        {{-- Konfirmasi Password --}}
                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Konfirmasi Password
                            </label>

                            <input
                                type="password"
                                name="password_confirmation"
                                class="form-control"
                                required>

                        </div>

                    </div>


                    {{-- Role --}}
                    <div class="mb-3">

                        <label class="form-label">
                            Role
                        </label>

                        <select
                            name="role_id"
                            class="form-select @error('role_id') is-invalid @enderror"
                            required>

                            <option value="">
                                -- Pilih Role --
                            </option>

                            @foreach ($roles as $role)

                                <option
                                    value="{{ $role->id }}"
                                    @selected(old('role_id') == $role->id)>

                                    {{ $role->name }}

                                </option>

                            @endforeach

                        </select>

                        @error('role_id')

                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>


                    {{-- Organisasi --}}
                    <div class="mb-3">

                        <label class="form-label">
                            Organisasi / Unit
                        </label>

                        <div class="border rounded p-3">

                            @forelse ($organizations as $organization)

                                <div class="form-check mb-2">

                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        name="organization_ids[]"
                                        value="{{ $organization->id }}"
                                        id="create_org_{{ $organization->id }}"

                                        @checked(
                                            in_array(
                                                $organization->id,
                                                old('organization_ids', [])
                                            )
                                        )
                                    >

                                    <label
                                        class="form-check-label"
                                        for="create_org_{{ $organization->id }}">

                                        <strong>
                                            {{ $organization->code }}
                                        </strong>

                                        — {{ $organization->name }}

                                        @if ($organization->type === 'INDUK')

                                            <span class="badge bg-primary">
                                                INDUK
                                            </span>

                                        @endif

                                    </label>

                                </div>

                            @empty

                                <span class="text-muted">
                                    Belum ada organisasi aktif.
                                </span>

                            @endforelse

                        </div>

                        @error('organization_ids')

                            <div class="text-danger mt-1">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>


                    {{-- Status --}}
                    <div class="mb-3">

                        <label class="form-label">
                            Status
                        </label>

                        <select
                            name="is_active"
                            class="form-select">

                            <option value="1" selected>
                                Aktif
                            </option>

                            <option value="0">
                                Tidak Aktif
                            </option>

                        </select>

                    </div>

                </div>


                {{-- FOOTER --}}
                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">

                        Batal

                    </button>

                    <button
                        type="submit"
                        class="btn btn-primary">

                        <i class="bi bi-save me-1"></i>
                        Simpan User

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>
