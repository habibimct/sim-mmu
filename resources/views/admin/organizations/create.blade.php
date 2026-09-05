@extends('adminlte::page')

@section('title', 'Tambah Organisasi')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Tambah Organisasi</h1>

        <a href="{{ route('admin.organizations.index') }}"
           class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i>
            Kembali
        </a>
    </div>
@stop

@section('content')

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Terjadi kesalahan:</strong>

            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">

        <div class="card-header">
            <h3 class="card-title">
                Form Organisasi
            </h3>
        </div>

        <form action="{{ route('admin.organizations.store') }}"
              method="POST">

            @csrf

            <div class="card-body">

                <div class="mb-3">
                    <label for="code" class="form-label">
                        Kode Organisasi
                    </label>

                    <input
                        type="text"
                        name="code"
                        id="code"
                        class="form-control @error('code') is-invalid @enderror"
                        value="{{ old('code') }}"
                        placeholder="Contoh: MI"
                        required
                    >

                    @error('code')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>


                <div class="mb-3">
                    <label for="name" class="form-label">
                        Nama Organisasi
                    </label>

                    <input
                        type="text"
                        name="name"
                        id="name"
                        class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name') }}"
                        placeholder="Contoh: Madrasah Ibtidaiyah"
                        required
                    >

                    @error('name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>


                <div class="mb-3">
                    <label for="type" class="form-label">
                        Jenis Organisasi
                    </label>

                    <select
                        name="type"
                        id="type"
                        class="form-select @error('type') is-invalid @enderror"
                        required
                    >
                        <option value="">-- Pilih Jenis --</option>

                        <option value="INDUK"
                            @selected(old('type') === 'INDUK')>
                            INDUK
                        </option>

                        <option value="UNIT"
                            @selected(old('type') === 'UNIT')>
                            Unit
                        </option>
                    </select>

                    @error('type')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>


                <div class="mb-3">
                    <label for="is_active" class="form-label">
                        Status
                    </label>

                    <select
                        name="is_active"
                        id="is_active"
                        class="form-select @error('is_active') is-invalid @enderror"
                        required
                    >
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
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i>
                    Simpan
                </button>

                <a href="{{ route('admin.organizations.index') }}"
                   class="btn btn-secondary">
                    Batal
                </a>
            </div>

        </form>

    </div>

@stop
