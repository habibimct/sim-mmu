@extends('adminlte::page')

@section('title', 'Profil Induk')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="mb-0">Profil Induk</h1>
    </div>
@stop

@section('content')

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-1"></i>
            {{ session('success') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
                aria-label="Close">
            </button>
        </div>
    @endif

    <div class="row">

        {{-- Logo --}}
        <div class="col-lg-4">

            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="bi bi-image me-1"></i>
                        Logo Induk
                    </h3>
                </div>

                <div class="card-body text-center">

                    <div class="mb-3">
                        @if ($induk->logo_path)
                            <img
                                src="{{ asset('storage/' . $induk->logo_path) }}"
                                alt="Logo {{ $induk->name }}"
                                class="img-fluid rounded"
                                style="max-height: 220px;"
                            >
                        @else
                            <div
                                class="border rounded d-flex align-items-center justify-content-center mx-auto"
                                style="width: 220px; height: 220px;"
                            >
                                <div class="text-muted">
                                    <i class="bi bi-building fs-1 d-block mb-2"></i>
                                    Belum ada logo
                                </div>
                            </div>
                        @endif
                    </div>

                    @if ($induk->logo_path)
                        <form
                            method="POST"
                            action="{{ route('admin.settings.profile-induk.logo.destroy') }}"
                            onsubmit="return confirm('Yakin ingin menghapus Logo Induk?')"
                        >
                            @csrf
                            @method('DELETE')

                            <button
                                type="submit"
                                class="btn btn-outline-danger"
                            >
                                <i class="bi bi-trash me-1"></i>
                                Hapus Logo
                            </button>
                        </form>
                    @endif

                    <div class="mt-3 text-muted small">
                        Format: JPG, JPEG, PNG, atau WebP.
                        Maksimal 5 MB.
                    </div>

                </div>
            </div>

        </div>

        {{-- Form Profil --}}
        <div class="col-lg-8">

            <div class="card card-primary">

                <div class="card-header">
                    <h3 class="card-title">
                        <i class="bi bi-building me-1"></i>
                        Informasi Induk
                    </h3>
                </div>

                <form
                    id="induk-profile-form"
                    method="POST"
                    action="{{ route('admin.settings.profile-induk.update') }}"
                    enctype="multipart/form-data"
                >
                    @csrf
                    @method('PUT')

                    <div class="card-body">

                        {{-- Nama --}}
                        <div class="mb-3">
                            <label for="name" class="form-label">
                                Nama Induk
                                <span class="text-danger">*</span>
                            </label>

                            <input
                                type="text"
                                id="name"
                                name="name"
                                class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $induk->name) }}"
                                required
                            >

                            @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- Alamat --}}
                        <div class="mb-3">
                            <label for="address" class="form-label">
                                Alamat
                            </label>

                            <textarea
                                id="address"
                                name="address"
                                rows="3"
                                class="form-control @error('address') is-invalid @enderror"
                            >{{ old('address', $induk->address) }}</textarea>

                            @error('address')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="row">

                            {{-- Telepon --}}
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">
                                    Telepon
                                </label>

                                <input
                                    type="text"
                                    id="phone"
                                    name="phone"
                                    class="form-control @error('phone') is-invalid @enderror"
                                    value="{{ old('phone', $induk->phone) }}"
                                >

                                @error('phone')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            {{-- Email --}}
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">
                                    Email
                                </label>

                                <input
                                    type="email"
                                    id="email"
                                    name="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    value="{{ old('email', $induk->email) }}"
                                >

                                @error('email')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                        </div>

                        {{-- Website --}}
                        <div class="mb-3">
                            <label for="website" class="form-label">
                                Website
                            </label>

                            <input
                                type="text"
                                id="website"
                                name="website"
                                class="form-control @error('website') is-invalid @enderror"
                                value="{{ old('website', $induk->website) }}"
                                placeholder="https://contoh.com"
                            >

                            @error('website')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- Logo --}}
                        <div class="mb-3">
                            <label for="logo" class="form-label">
                                {{ $induk->logo_path ? 'Ganti Logo' : 'Upload Logo' }}
                            </label>

                            <input
                                type="file"
                                id="logo"
                                name="logo"
                                class="form-control @error('logo') is-invalid @enderror"
                                accept="image/jpeg,image/png,image/webp"
                            >

                            @error('logo')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                            <div class="form-text">
                                Logo akan otomatis dikompresi dan disimpan dalam format WebP.
                            </div>
                        </div>

                    </div>

                    <div class="card-footer text-end">

                        <button
                            type="submit"
                            class="btn btn-primary"
                        >
                            <i class="bi bi-save me-1"></i>
                            Simpan Perubahan
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

@stop
