@extends('adminlte::page')

@section('title', 'Daftar Guru')

@section('content_header') <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0">Daftar Guru</h1>

        <div class="d-flex gap-2">
            {{-- Import Guru --}}
            @can('teachers.manage')
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalImportGuru">
                    <i class="bi bi-upload me-1"></i>
                    Import Guru
                </button>
            @endcan

            @can('teachers.view')
                <a href="{{ route('admin.teachers.export', request()->query()) }}" class="btn btn-success">

                    <i class="bi bi-file-earmark-excel me-1"></i>
                    Export
                </a>
            @endcan

            {{-- Tambah Guru --}}
            @can('teachers.manage')
                <a href="{{ route('admin.teachers.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg me-1"></i>
                    Tambah Guru
                </a>
            @endcan
        </div>
    </div>


@stop

@section('content')


    {{-- Pesan sukses --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-1"></i>
            {{ session('success') }}

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Pesan error --}}
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-1"></i>
            {{ session('error') }}

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Kesalahan import --}}
    @if (session('import_errors'))
        <div class="alert alert-danger">

            <div class="d-flex align-items-center mb-2">

                <i class="bi bi-exclamation-triangle-fill me-2"></i>

                <strong>
                    Import Guru gagal.
                </strong>

            </div>

            <p class="mb-2">
                Tidak ada data Guru yang disimpan karena terdapat
                kesalahan pada file import.
            </p>

            <ul class="mb-0">

                @foreach (session('import_errors') as $error)
                    <li>
                        <strong>
                            Baris {{ $error['row'] }}:
                        </strong>

                        {{ $error['message'] }}
                    </li>
                @endforeach

            </ul>

        </div>
    @endif

    <div class="card">

        <div class="card-header">
            <h3 class="card-title">
                Data Guru
            </h3>
        </div>

        <div class="card-body">

            {{-- Pencarian dan Filter --}}
            <form method="GET" action="{{ route('admin.teachers.index') }}" class="row g-2 mb-3">

                <div class="col-md-4">
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                        placeholder="Cari NIP atau nama guru...">
                </div>

                <div class="col-md-3">
                    <select name="organization_id" class="form-control">

                        <option value="">
                            Semua Organisasi
                        </option>

                        @foreach ($organizations as $organization)
                            <option value="{{ $organization->id }}" @selected(request('organization_id') == $organization->id)>
                                {{ $organization->name }}
                            </option>
                        @endforeach

                    </select>
                </div>

                <div class="col-md-3">
                    <select name="status" class="form-control">

                        <option value="">
                            Semua Status
                        </option>

                        <option value="active" @selected(request('status') === 'active')>
                            Aktif
                        </option>

                        <option value="inactive" @selected(request('status') === 'inactive')>
                            Nonaktif
                        </option>

                    </select>
                </div>

                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i>
                        Cari
                    </button>

                    <a href="{{ route('admin.teachers.index') }}" class="btn btn-secondary">
                        Reset
                    </a>
                </div>

            </form>

            {{-- Tabel Guru --}}
            <div class="table-responsive">

                <table class="table table-bordered table-hover align-middle">

                    <thead class="table-light">
                        <tr>
                            <th width="60">No.</th>
                            <th>NIK</th>
                            <th>Nama</th>
                            <th>Jenis Kelamin</th>
                            <th>Organisasi</th>
                            <th>No. HP</th>
                            <th>Status</th>
                            <th>Akun</th>
                            <th>Password</th>
                            <th width="250">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse ($teachers as $teacher)
                            <tr>
                                <td>
                                    {{ $teachers->firstItem() + $loop->index }}
                                </td>

                                <td>
                                    {{ $teacher->nik ?? '-' }}
                                </td>

                                <td>
                                    {{ $teacher->name }}
                                </td>

                                <td>
                                    {{ $teacher->gender === 'male' ? 'Laki-laki' : 'Perempuan' }}
                                </td>

                                <td>
                                    {{ $teacher->organizations->pluck('name')->join(', ') }}
                                </td>

                                <td>
                                    {{ $teacher->phone ?? '-' }}
                                </td>

                                <td>
                                    @if ($teacher->is_active)
                                        <span class="badge bg-success">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="badge bg-danger">
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>

                                {{-- Akun --}}
                                <td>
                                    @if ($teacher->user)
                                        @if ($teacher->user->is_active)
                                            <span class="badge bg-success">
                                                Aktif
                                            </span>
                                        @else
                                            <span class="badge bg-danger">
                                                Nonaktif
                                            </span>
                                        @endif
                                    @else
                                        <span class="badge bg-secondary">
                                            Belum dibuat
                                        </span>
                                    @endif
                                </td>

                                {{-- Password awal --}}
                                <td>
                                    @if ($teacher->user && $teacher->initial_password)
                                        <span class="font-monospace">
                                            {{ $teacher->initial_password }}
                                        </span>
                                    @else
                                        <span class="text-muted">
                                            -
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    {{-- Edit --}}
                                    <a href="{{ route('admin.teachers.edit', $teacher) }}" class="btn btn-sm btn-warning"
                                        title="Edit Guru">
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                    {{-- Akun Guru --}}
                                    @if ($teacher->user)
                                        {{-- Reset Password --}}
                                        <form action="{{ route('admin.teachers.reset-password', $teacher) }}"
                                            method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')

                                            <button type="submit" class="btn btn-sm btn-info"
                                                onclick="return confirm('Apakah Anda yakin ingin mereset password guru {{ $teacher->name }}?')"
                                                title="Reset Password">
                                                <i class="bi bi-key"></i>
                                            </button>
                                        </form>
                                    @else
                                        {{-- Buat Akun --}}
                                        <form action="{{ route('admin.teachers.create-account', $teacher) }}"
                                            method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')

                                            <button type="submit" class="btn btn-sm btn-primary"
                                                onclick="return confirm('Apakah Anda yakin ingin membuat akun untuk guru {{ $teacher->name }}?')"
                                                title="Buat Akun Guru">
                                                <i class="bi bi-person-plus"></i>
                                            </button>
                                        </form>
                                    @endif

                                    {{-- Aktifkan / Nonaktifkan --}}
                                    <form action="{{ route('admin.teachers.toggle-status', $teacher) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        @method('PATCH')

                                        @if ($teacher->is_active)
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                onclick="return confirm('Apakah Anda yakin ingin menonaktifkan guru {{ $teacher->name }}?')"
                                                title="Nonaktifkan Guru">
                                                <i class="bi bi-person-slash"></i>
                                            </button>
                                        @else
                                            <button type="submit" class="btn btn-sm btn-success"
                                                onclick="return confirm('Apakah Anda yakin ingin mengaktifkan guru {{ $teacher->name }}?')"
                                                title="Aktifkan Guru">
                                                <i class="bi bi-person-check"></i>
                                            </button>
                                        @endif

                                    </form>

                                </td>
                            </tr>

                        @empty

                            <tr>
                                <td colspan="10" class="text-center text-muted py-4">
                                    Belum ada data guru.
                                </td>
                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

            {{-- Pagination --}}
            @if ($teachers->hasPages())
                <div class="mt-3">
                    {{ $teachers->links() }}
                </div>
            @endif

        </div>

    </div>

    {{-- Modal Import Guru --}}
    <div class="modal fade" id="modalImportGuru" tabindex="-1" aria-labelledby="modalImportGuruLabel"
        aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered">

            <div class="modal-content">

                {{-- Header --}}
                <div class="modal-header">

                    <h5 class="modal-title" id="modalImportGuruLabel">
                        <i class="bi bi-upload me-1"></i>
                        Import Data Guru
                    </h5>

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>

                </div>

                {{-- Body --}}
                <div class="modal-body">

                    <div class="alert alert-info">

                        <i class="bi bi-info-circle me-1"></i>

                        Gunakan template yang telah disediakan
                        untuk melakukan import data Guru.

                    </div>

                    <form action="{{ route('admin.teachers.import.store') }}" method="POST"
                        enctype="multipart/form-data" id="formImportGuru">

                        @csrf

                        {{-- Unit tujuan untuk Admin Global --}}
                        @if ($isGlobalOrganizationManager)

                            <div class="mb-3">

                                <label for="organizationImportGuru" class="form-label">

                                    Unit Tujuan
                                    <span class="text-danger">*</span>

                                </label>

                                <select name="organization_id" id="organizationImportGuru" class="form-select" required>

                                    <option value="">
                                        -- Pilih Unit Tujuan --
                                    </option>

                                    @foreach ($organizations as $organization)
                                        @if ($organization->parent_id)
                                            <option value="{{ $organization->id }}">
                                                {{ $organization->name }}
                                            </option>
                                        @endif
                                    @endforeach

                                </select>

                                <small class="text-muted">
                                    Guru yang diimport akan ditempatkan
                                    pada Unit yang dipilih.
                                </small>

                            </div>
                        @else
                            {{-- Admin Unit --}}
                            <div class="mb-3">

                                <label class="form-label">
                                    Unit
                                </label>

                                <input type="text" class="form-control"
                                    value="{{ $organizations->first()->name ?? '-' }}" readonly>

                                <small class="text-muted">
                                    Unit ditentukan otomatis berdasarkan
                                    kewenangan Anda.
                                </small>

                            </div>

                        @endif

                        {{-- File --}}
                        <div class="mb-3">

                            <label for="fileImportGuru" class="form-label">

                                File Excel
                                <span class="text-danger">*</span>

                            </label>

                            <input type="file" name="file" id="fileImportGuru" class="form-control"
                                accept=".xlsx,.xls" required>

                            <small class="text-muted">
                                Format .xlsx atau .xls, maksimal 10 MB.
                            </small>

                        </div>

                    </form>

                    <div class="alert alert-warning mb-0">

                        <i class="bi bi-shield-lock me-1"></i>

                        <strong>Catatan:</strong>

                        Template tidak memiliki kolom Unit.
                        Penempatan Guru ditentukan oleh sistem
                        berdasarkan kewenangan pengguna.

                    </div>

                </div>

                {{-- Footer --}}
                <div class="modal-footer">

                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">

                        Tutup

                    </button>

                    <a href="{{ route('admin.teachers.import.template') }}" class="btn btn-outline-success">

                        <i class="bi bi-file-earmark-excel me-1"></i>
                        Template

                    </a>

                    <button type="submit" form="formImportGuru" class="btn btn-outline-success">

                        <i class="bi bi-upload me-1"></i>
                        Import

                    </button>

                </div>

            </div>

        </div>

    </div>

@stop
