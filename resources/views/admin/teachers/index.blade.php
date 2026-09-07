@extends('adminlte::page')

@section('title', 'Daftar Guru')

@section('content_header')

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">

        <h1 class="m-0">
            Daftar Guru
        </h1>

        <div class="d-flex gap-2">

            {{-- Import Guru --}}
            @can('teachers.manage')
                <button type="button"
                    class="btn btn-success"
                    data-bs-toggle="modal"
                    data-bs-target="#modalImportGuru">

                    <i class="bi bi-upload me-1"></i>
                    Import Guru

                </button>
            @endcan


            {{-- Export --}}
            @can('teachers.view')
                <a href="{{ route('admin.teachers.export', request()->query()) }}"
                    class="btn btn-success">

                    <i class="bi bi-file-earmark-excel me-1"></i>
                    Export

                </a>
            @endcan


            {{-- Tambah Guru --}}
            @can('teachers.manage')
                <button type="button"
                    class="btn btn-primary"
                    data-bs-toggle="modal"
                    data-bs-target="#modalTambahGuru">

                    <i class="bi bi-plus-lg me-1"></i>
                    Tambah Guru

                </button>
            @endcan

        </div>

    </div>

@stop


@section('content')

    {{-- =========================================================
        PESAN SUKSES
    ========================================================== --}}
    @if (session('success'))

        <div class="alert alert-success alert-dismissible fade show"
            role="alert">

            <i class="bi bi-check-circle me-1"></i>

            {{ session('success') }}

            <button type="button"
                class="btn-close"
                data-bs-dismiss="alert"
                aria-label="Close">
            </button>

        </div>

    @endif


    {{-- =========================================================
        PESAN ERROR
    ========================================================== --}}
    @if (session('error'))

        <div class="alert alert-danger alert-dismissible fade show"
            role="alert">

            <i class="bi bi-exclamation-triangle me-1"></i>

            {{ session('error') }}

            <button type="button"
                class="btn-close"
                data-bs-dismiss="alert"
                aria-label="Close">
            </button>

        </div>

    @endif


    {{-- =========================================================
        KESALAHAN IMPORT
    ========================================================== --}}
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


    {{-- =========================================================
        DATA GURU
    ========================================================== --}}
    <div class="card">

        <div class="card-header">

            <h3 class="card-title">
                Data Guru
            </h3>

        </div>


        <div class="card-body">

            {{-- =================================================
                PENCARIAN DAN FILTER
            ================================================== --}}
            <form method="GET"
                action="{{ route('admin.teachers.index') }}"
                class="row g-2 mb-3">

                {{-- Pencarian --}}
                <div class="col-md-4">

                    <input type="text"
                        name="search"
                        value="{{ request('search') }}"
                        class="form-control"
                        placeholder="Cari NIK atau nama guru...">

                </div>


                {{-- Organisasi --}}
                <div class="col-md-3">

                    <select name="organization_id"
                        class="form-select">

                        <option value="">
                            Semua Organisasi
                        </option>

                        @foreach ($organizations as $organization)

                            <option value="{{ $organization->id }}"
                                @selected(request('organization_id') == $organization->id)>

                                {{ $organization->name }}

                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- Status --}}
                <div class="col-md-3">

                    <select name="status"
                        class="form-select">

                        <option value="">
                            Semua Status
                        </option>

                        <option value="active"
                            @selected(request('status') === 'active')>

                            Aktif

                        </option>

                        <option value="inactive"
                            @selected(request('status') === 'inactive')>

                            Nonaktif

                        </option>

                    </select>

                </div>


                {{-- Tombol --}}
                <div class="col-md-2 d-flex gap-2">

                    <button type="submit"
                        class="btn btn-primary">

                        <i class="bi bi-search"></i>
                        Cari

                    </button>


                    <a href="{{ route('admin.teachers.index') }}"
                        class="btn btn-secondary">

                        Reset

                    </a>

                </div>

            </form>


            {{-- =================================================
                TABEL GURU
            ================================================== --}}
            <div class="table-responsive">

                <table class="table table-bordered table-hover align-middle">

                    <thead class="table-light">

                        <tr>

                            <th width="60">
                                No.
                            </th>

                            <th>
                                NIK
                            </th>

                            <th>
                                Nama
                            </th>

                            <th>
                                Jenis Kelamin
                            </th>

                            <th>
                                Tempat Lahir
                            </th>

                            <th>
                                Tanggal Lahir
                            </th>

                            <th>
                                Organisasi
                            </th>

                            <th>
                                Status
                            </th>

                            <th>
                                Akun
                            </th>

                            <th>
                                Password
                            </th>

                            <th width="250">
                                Aksi
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse ($teachers as $teacher)

                            <tr>

                                {{-- No --}}
                                <td>
                                    {{ $teachers->firstItem() + $loop->index }}
                                </td>


                                {{-- NIK --}}
                                <td>
                                    {{ $teacher->nik ?? '-' }}
                                </td>


                                {{-- Nama --}}
                                <td>
                                    {{ $teacher->name }}
                                </td>


                                {{-- Jenis Kelamin --}}
                                <td>

                                    @if ($teacher->gender === 'male')

                                        Laki-laki

                                    @elseif ($teacher->gender === 'female')

                                        Perempuan

                                    @else

                                        -

                                    @endif

                                </td>


                                {{-- Tempat Lahir --}}
                                <td>
                                    {{ $teacher->birth_place ?? '-' }}
                                </td>


                                {{-- Tanggal Lahir --}}
                                <td>

                                    @if ($teacher->birth_date)

                                        {{ $teacher->birth_date->format('d/m/Y') }}

                                    @else

                                        -

                                    @endif

                                </td>


                                {{-- Organisasi --}}
                                <td>

                                    {{ $teacher->organizations->pluck('name')->join(', ') }}

                                </td>


                                {{-- Status Guru --}}
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


                                {{-- Password Awal --}}
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


                                {{-- =================================================
                                    AKSI
                                ================================================== --}}
                                <td>

                                    {{-- Edit --}}
                                    @can('teachers.manage')

                                        <button type="button"
                                            class="btn btn-sm btn-outline-primary"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalEditGuru{{ $teacher->id }}"
                                            title="Edit Guru">

                                            <i class="bi bi-pencil"></i>

                                        </button>

                                    @endcan


                                    {{-- Akun Guru --}}
                                    @can('teachers.manage')

                                        @if ($teacher->user)

                                            {{-- Reset Password --}}
                                            <form action="{{ route('admin.teachers.reset-password', $teacher) }}"
                                                method="POST"
                                                class="d-inline">

                                                @csrf
                                                @method('PATCH')

                                                <button type="submit"
                                                    class="btn btn-sm btn-info"
                                                    onclick="return confirm('Apakah Anda yakin ingin mereset password guru {{ $teacher->name }}?')"
                                                    title="Reset Password">

                                                    <i class="bi bi-key"></i>

                                                </button>

                                            </form>

                                        @else

                                            {{-- Buat Akun --}}
                                            <form action="{{ route('admin.teachers.create-account', $teacher) }}"
                                                method="POST"
                                                class="d-inline">

                                                @csrf
                                                @method('PATCH')

                                                <button type="submit"
                                                    class="btn btn-sm btn-primary"
                                                    onclick="return confirm('Apakah Anda yakin ingin membuat akun untuk guru {{ $teacher->name }}?')"
                                                    title="Buat Akun Guru">

                                                    <i class="bi bi-person-plus"></i>

                                                </button>

                                            </form>

                                        @endif

                                    @endcan


                                    {{-- Aktifkan / Nonaktifkan --}}
                                    @can('teachers.manage')

                                        <form action="{{ route('admin.teachers.toggle-status', $teacher) }}"
                                            method="POST"
                                            class="d-inline">

                                            @csrf
                                            @method('PATCH')

                                            @if ($teacher->is_active)

                                                <button type="submit"
                                                    class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Apakah Anda yakin ingin menonaktifkan guru {{ $teacher->name }}?')"
                                                    title="Nonaktifkan Guru">

                                                    <i class="bi bi-person-slash"></i>

                                                </button>

                                            @else

                                                <button type="submit"
                                                    class="btn btn-sm btn-success"
                                                    onclick="return confirm('Apakah Anda yakin ingin mengaktifkan guru {{ $teacher->name }}?')"
                                                    title="Aktifkan Guru">

                                                    <i class="bi bi-person-check"></i>

                                                </button>

                                            @endif

                                        </form>

                                    @endcan


                                    {{-- Hapus --}}
                                    @can('teachers.manage')

                                        @if ($teacher->attendances_count > 0)

                                            <button type="button"
                                                class="btn btn-sm btn-secondary"
                                                disabled
                                                title="Guru tidak dapat dihapus karena sudah memiliki riwayat absensi">

                                                <i class="bi bi-trash"></i>

                                            </button>

                                        @else

                                            <form action="{{ route('admin.teachers.destroy', $teacher) }}"
                                                method="POST"
                                                class="d-inline"
                                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus guru {{ $teacher->name }}?')">

                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                    class="btn btn-sm btn-outline-danger"
                                                    title="Hapus Guru">

                                                    <i class="bi bi-trash"></i>

                                                </button>

                                            </form>

                                        @endif

                                    @endcan

                                </td>

                            </tr>


                            {{-- Modal Edit Guru --}}
                            @can('teachers.manage')
                                @include('admin.teachers.partials.modal-edit')
                            @endcan


                        @empty

                            <tr>

                                <td colspan="11"
                                    class="text-center text-muted py-4">

                                    Belum ada data guru.

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>


            {{-- =================================================
                PAGINATION
            ================================================== --}}
            @if ($teachers->hasPages())

                <div class="mt-3">

                    {{ $teachers->links() }}

                </div>

            @endif

        </div>

    </div>


    {{-- =========================================================
        MODAL TAMBAH GURU
    ========================================================== --}}
    @can('teachers.manage')
        @include('admin.teachers.partials.modal-create')
    @endcan


    {{-- =========================================================
        MODAL IMPORT GURU
    ========================================================== --}}
    @can('teachers.manage')
        @include('admin.teachers.partials.modal-import')
    @endcan


    {{-- =========================================================
        BUKA KEMBALI MODAL JIKA VALIDASI GAGAL
    ========================================================== --}}

    @if ($errors->any() && old('_form') === 'create')

        <script>
            document.addEventListener('DOMContentLoaded', function() {

                const modalElement =
                    document.getElementById('modalTambahGuru');

                if (modalElement) {

                    const modal =
                        new bootstrap.Modal(modalElement);

                    modal.show();

                }

            });
        </script>

    @endif


    @if ($errors->any() && old('_form') === 'edit' && old('_user_id'))

        <script>
            document.addEventListener('DOMContentLoaded', function() {

                const modalElement =
                    document.getElementById(
                        'modalEditGuru{{ old('_user_id') }}'
                    );

                if (modalElement) {

                    const modal =
                        new bootstrap.Modal(modalElement);

                    modal.show();

                }

            });
        </script>

    @endif

@stop
