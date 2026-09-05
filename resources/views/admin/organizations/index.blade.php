@extends('adminlte::page')

@section('title', 'Organisasi')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Organisasi</h1>

        <a href="{{ route('admin.organizations.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i>
            Tambah Organisasi
        </a>
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

    @if ($errors->has('status'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle"></i>
            {{ $errors->first('status') }}

            <button type="button" class="btn-close" data-bs-dismiss="alert">
            </button>
        </div>
    @endif

    <div class="card">

        <div class="card-header">
            <h3 class="card-title">
                Daftar Organisasi dan Unit
            </h3>
        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-bordered table-hover align-middle">

                    <thead>
                        <tr>
                            <th width="60">No</th>
                            <th>Kode</th>
                            <th>Nama</th>
                            <th>Jenis</th>
                            <th>Status</th>
                            <th width="120">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse ($organizations as $organization)
                            <tr>
                                <td>
                                    {{ $loop->iteration }}
                                </td>

                                <td>
                                    <strong>
                                        {{ $organization->code }}
                                    </strong>
                                </td>

                                <td>
                                    {{ $organization->name }}
                                </td>

                                <td>
                                    @if ($organization->type === 'INDUK')
                                        <span class="badge text-bg-primary">
                                            INDUK
                                        </span>
                                    @else
                                        <span class="badge text-bg-info">
                                            UNIT
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    @if ($organization->is_active)
                                        <span class="badge text-bg-success">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="badge text-bg-secondary">
                                            Tidak Aktif
                                        </span>
                                    @endif
                                </td>

                                <td>

                                    <a href="{{ route('admin.organizations.edit', $organization) }}"
                                        class="btn btn-sm btn-warning" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>


                                    @if ($organization->type !== 'INDUK')
                                        <form action="{{ route('admin.organizations.toggle-status', $organization) }}"
                                            method="POST" class="d-inline"
                                            onsubmit="return confirm('Apakah Anda yakin ingin mengubah status organisasi ini?');">

                                            @csrf
                                            @method('PATCH')

                                            @if ($organization->is_active)
                                                <button type="submit" class="btn btn-sm btn-danger" title="Nonaktifkan">
                                                    <i class="bi bi-toggle-on"></i>
                                                </button>
                                            @else
                                                <button type="submit" class="btn btn-sm btn-success" title="Aktifkan">
                                                    <i class="bi bi-toggle-off"></i>
                                                </button>
                                            @endif

                                        </form>
                                    @endif

                                </td>
                            </tr>

                        @empty

                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    Belum ada data organisasi.
                                </td>
                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

@stop
