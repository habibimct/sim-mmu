{{-- =================================================
    MODAL EDIT USER
================================================= --}}

@php

    $selectedOrganizations = old('organization_ids', $user->organizations->pluck('id')->toArray());

    $selectedRole = old('role_id', $user->roles->first()?->id);

@endphp


<div class="modal fade" id="modalEditUser{{ $user->id }}" tabindex="-1"
    aria-labelledby="modalEditUserLabel{{ $user->id }}" aria-hidden="true">

    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content">

            <form method="POST" action="{{ route('admin.users.update', $user) }}">

                @csrf
                @method('PUT')


                <input type="hidden" name="_form" value="edit">

                <input type="hidden" name="_user_id" value="{{ $user->id }}">


                {{-- HEADER --}}
                <div class="modal-header">

                    <h5 class="modal-title" id="modalEditUserLabel{{ $user->id }}">

                        <i class="bi bi-person-gear me-2"></i>
                        Edit User

                    </h5>

                    <button type="button" class="btn-close" data-bs-dismiss="modal">
                    </button>

                </div>


                {{-- BODY --}}
                <div class="modal-body">

                    {{-- Nama --}}
                    <div class="mb-3">

                        <label class="form-label">
                            Nama
                        </label>

                        <input type="text" name="name" value="{{ old('name', $user->name) }}"
                            class="form-control @error('name') is-invalid @enderror" required>

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

                        <input type="email" name="email" value="{{ old('email', $user->email) }}"
                            class="form-control @error('email') is-invalid @enderror" required>

                        @error('email')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    {{-- Password Baru --}}
                    <div class="row">

                        {{-- Password Baru --}}
                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Password Baru
                            </label>

                            <input type="password" name="password"
                                class="form-control @error('password') is-invalid @enderror"
                                autocomplete="new-password">

                            <small class="text-muted">
                                Kosongkan jika password tidak ingin diubah.
                            </small>

                            @error('password')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>


                        {{-- Konfirmasi Password --}}
                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Konfirmasi Password Baru
                            </label>

                            <input type="password" name="password_confirmation" class="form-control"
                                autocomplete="new-password">

                        </div>

                    </div>


                    {{-- Role --}}
                    <div class="mb-3">

                        <label class="form-label">
                            Role
                        </label>

                        <select name="role_id" class="form-select @error('role_id') is-invalid @enderror" required>

                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}" @selected($selectedRole == $role->id)>

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

                                    <input class="form-check-input" type="checkbox" name="organization_ids[]"
                                        value="{{ $organization->id }}"
                                        id="edit_org_{{ $user->id }}_{{ $organization->id }}"
                                        @checked(in_array($organization->id, $selectedOrganizations))>

                                    <label class="form-check-label"
                                        for="edit_org_{{ $user->id }}_{{ $organization->id }}">

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

                        <select name="is_active" class="form-select">

                            <option value="1" @selected($user->is_active)>
                                Aktif
                            </option>

                            <option value="0" @selected(!$user->is_active)>
                                Tidak Aktif
                            </option>

                        </select>

                    </div>

                </div>


                {{-- FOOTER --}}
                <div class="modal-footer">

                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">

                        Batal

                    </button>

                    <button type="submit" class="btn btn-primary">

                        <i class="bi bi-save me-1"></i>
                        Simpan Perubahan

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>
