@extends('adminlte::page')

@section('title', 'Daftar User')

@section('content_header')
    <h1>Daftar User</h1>
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

            <div class="card-tools">
                <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-circle"></i>
                    Tambah User
                </a>
            </div>
        </div>


        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-bordered table-hover">

                    <thead>
                        <tr>
                            <th width="50">No</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Organisasi</th>
                            <th>Status</th>
                            <th width="120">Aksi</th>
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

                                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-warning"
                                        title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="7" class="text-center text-muted">

                                    Belum ada data user.

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

@stop
