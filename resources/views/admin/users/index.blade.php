@extends('adminlte::page')

@section('title', 'Daftar User')

@section('content_header')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        <h1>Daftar User</h1>

        <div class="card-tools">

            {{-- Template --}}
            <a href="{{ route('admin.users.template') }}" class="btn btn-outline-success btn-sm">

                <i class="bi bi-file-earmark-excel"></i>
                Template

            </a>


            {{-- Import --}}
            <button type="button" class="btn btn-outline-info btn-sm" data-bs-toggle="modal" data-bs-target="#modalImportUser">

                <i class="bi bi-upload"></i>
                Import

            </button>


            {{-- Export --}}
            <a href="{{ route('admin.users.export') }}" class="btn btn-outline-secondary btn-sm">

                <i class="bi bi-download"></i>
                Export

            </a>


            {{-- Tambah --}}
            <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahUser">

                <i class="bi bi-plus-circle"></i>
                Tambah User

            </button>

        </div>
    </div>
@stop

@section('content')

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle"></i>
            {{ session('success') }}

            <button type="button" class="btn-close" data-bs-dismiss="alert">
            </button>
        </div>
    @endif


    <div class="card">

        <div class="card-header">
            <h3 class="card-title">
                Data User
            </h3>
        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-bordered table-hover">

                    <thead>
                        <tr>
                            <th width="50">No</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Password</th>
                            <th>Role</th>
                            <th>Organisasi</th>
                            <th>Status</th>
                            <th width="150">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse ($users as $user)

                            <tr>

                                <td>
                                    {{ $loop->iteration }}
                                </td>


                                <td>
                                    {{ $user->name }}
                                </td>


                                <td>
                                    {{ $user->email }}
                                </td>

                                <td>
                                    @if ($user->initial_password)
                                        <span class="font-monospace">
                                            {{ $user->initial_password }}
                                        </span>
                                    @else
                                        <span class="text-muted">
                                            —
                                        </span>
                                    @endif
                                </td>

                                <td>

                                    @forelse ($user->roles as $role)
                                        <span class="badge bg-primary">
                                            {{ $role->name }}
                                        </span>

                                    @empty

                                        <span class="text-muted">
                                            Belum ada role
                                        </span>
                                    @endforelse

                                </td>


                                <td>

                                    @forelse ($user->organizations as $organization)
                                        <span class="badge bg-info text-dark">
                                            {{ $organization->code }}
                                        </span>

                                    @empty

                                        <span class="text-muted">
                                            Belum ada organisasi
                                        </span>
                                    @endforelse

                                </td>


                                <td>

                                    @if ($user->is_active)
                                        <span class="badge bg-success">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="badge bg-danger">
                                            Tidak Aktif
                                        </span>
                                    @endif

                                </td>


                                <td>

                                    {{-- EDIT --}}
                                    <button type="button" class="btn btn-sm btn-warning" title="Edit"
                                        data-bs-toggle="modal" data-bs-target="#modalEditUser{{ $user->id }}">

                                        <i class="bi bi-pencil"></i>

                                    </button>


                                    {{-- RESET PASSWORD --}}
                                    <form action="{{ route('admin.users.reset-password', $user) }}" method="POST"
                                        class="d-inline"
                                        onsubmit="return confirm('Reset password user ini? Password baru akan dibuat secara otomatis.')">

                                        @csrf
                                        @method('PATCH')

                                        <button type="submit" class="btn btn-sm btn-info" title="Reset Password">

                                            <i class="bi bi-key"></i>

                                        </button>

                                    </form>


                                    {{-- HAPUS --}}
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                                        class="d-inline"
                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus user {{ $user->name }}?')">

                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="btn btn-sm btn-danger" title="Hapus">

                                            <i class="bi bi-trash"></i>

                                        </button>

                                    </form>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="8" class="text-center text-muted">

                                    Belum ada data user.

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>
    </div>


    @include('admin.users.partials.modal-import')

    {{-- =================================================
        MODAL TAMBAH USER
    ================================================= --}}
    @include('admin.users.partials.modal-create')


    {{-- =================================================
        MODAL EDIT USER
    ================================================= --}}
    @foreach ($users as $user)
        @include('admin.users.partials.modal-edit', [
            'user' => $user,
            'roles' => $roles,
            'organizations' => $organizations,
        ])
    @endforeach

    @if ($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function() {

                @if (old('_form') === 'create')

                    const modalCreateElement =
                        document.getElementById('modalTambahUser');

                    if (modalCreateElement) {

                        const modalCreate =
                            new bootstrap.Modal(modalCreateElement);

                        modalCreate.show();
                    }
                @elseif (old('_form') === 'edit' && old('_user_id'))

                    const modalEditElement =
                        document.getElementById(
                            'modalEditUser{{ old('_user_id') }}'
                        );

                    if (modalEditElement) {

                        const modalEdit =
                            new bootstrap.Modal(modalEditElement);

                        modalEdit.show();
                    }
                @endif

            });
        </script>
    @endif


@stop
