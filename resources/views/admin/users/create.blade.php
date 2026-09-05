@extends('adminlte::page')

@section('title', 'Tambah User')

@section('content_header')
    <h1>Tambah User</h1>
@stop

@section('content')

    <div class="card">

        <div class="card-header">
            <h3 class="card-title">
                Form Tambah User
            </h3>
        </div>

        <form method="POST"
              action="{{ route('admin.users.store') }}">

            @csrf

            <div class="card-body">

                {{-- Nama --}}
                <div class="mb-3">

                    <label for="name"
                           class="form-label">
                        Nama
                    </label>

                    <input type="text"
                           name="name"
                           id="name"
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

                    <label for="email"
                           class="form-label">
                        Email
                    </label>

                    <input type="email"
                           name="email"
                           id="email"
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
                <div class="mb-3">

                    <label for="password"
                           class="form-label">
                        Password
                    </label>

                    <input type="password"
                           name="password"
                           id="password"
                           class="form-control @error('password') is-invalid @enderror"
                           required>

                    @error('password')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                {{-- Konfirmasi Password --}}
                <div class="mb-3">

                    <label for="password_confirmation"
                           class="form-label">
                        Konfirmasi Password
                    </label>

                    <input type="password"
                           name="password_confirmation"
                           id="password_confirmation"
                           class="form-control"
                           required>

                </div>


                {{-- Role --}}
                <div class="mb-3">

                    <label for="role_id"
                           class="form-label">
                        Role
                    </label>

                    <select name="role_id"
                            id="role_id"
                            class="form-select @error('role_id') is-invalid @enderror"
                            required>

                        <option value="">
                            -- Pilih Role --
                        </option>

                        @foreach ($roles as $role)

                            <option value="{{ $role->id }}"
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

                                <input class="form-check-input"
                                       type="checkbox"
                                       name="organization_ids[]"
                                       value="{{ $organization->id }}"
                                       id="organization_{{ $organization->id }}"
                                       @checked(
                                           in_array(
                                               $organization->id,
                                               old('organization_ids', [])
                                           )
                                       )>

                                <label class="form-check-label"
                                       for="organization_{{ $organization->id }}">

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

                    @error('organization_ids.*')
                        <div class="text-danger mt-1">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                {{-- Status --}}
                <div class="mb-3">

                    <label for="is_active"
                           class="form-label">
                        Status
                    </label>

                    <select name="is_active"
                            id="is_active"
                            class="form-select @error('is_active') is-invalid @enderror">

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


            <div class="card-footer">

                <button type="submit"
                        class="btn btn-primary">

                    <i class="bi bi-save"></i>
                    Simpan

                </button>

                <a href="{{ route('admin.users.index') }}"
                   class="btn btn-secondary">

                    <i class="bi bi-arrow-left"></i>
                    Kembali

                </a>

            </div>

        </form>

    </div>

@stop
