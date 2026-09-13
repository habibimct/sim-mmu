@extends('adminlte::page')

@section('title', 'Profil Unit')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="mb-0">Profil Unit</h1>
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

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>
                <i class="bi bi-exclamation-triangle me-1"></i>
                Periksa kembali data yang dimasukkan.
            </strong>

            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
                aria-label="Close">
            </button>
        </div>
    @endif

    {{-- Pilih Unit --}}
    <div class="card card-primary card-outline mb-4">

        <div class="card-header">
            <h3 class="card-title">
                <i class="bi bi-building me-1"></i>
                Pilih Unit
            </h3>
        </div>

        <div class="card-body">

            @if ($units->isEmpty())

                <div class="alert alert-warning mb-0">
                    <i class="bi bi-exclamation-circle me-1"></i>
                    Belum ada Unit yang aktif.
                </div>

            @else

                <form
                    method="GET"
                    action="{{ route('admin.settings.profile-unit.index') }}"
                    id="unit-selector-form"
                >
                    <div class="row align-items-end">

                        <div class="col-md-8 col-lg-6">
                            <label for="unit_id" class="form-label">
                                Unit
                            </label>

                            <select
                                name="unit_id"
                                id="unit_id"
                                class="form-select"
                            >
                                @foreach ($units as $unit)
                                    <option
                                        value="{{ $unit->id }}"
                                        @selected(
                                            $selectedUnit &&
                                            $selectedUnit->id === $unit->id
                                        )
                                    >
                                        {{ $unit->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-auto mt-3 mt-md-0">
                            <button
                                type="submit"
                                class="btn btn-primary"
                            >
                                <i class="bi bi-arrow-right-circle me-1"></i>
                                Tampilkan
                            </button>
                        </div>

                    </div>
                </form>

            @endif

        </div>

    </div>


    @if ($selectedUnit)

        <div class="row">

            {{-- Logo Unit --}}
            <div class="col-lg-4">

                <div class="card card-primary card-outline">

                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="bi bi-image me-1"></i>
                            Logo Unit
                        </h3>
                    </div>

                    <div class="card-body text-center">

                        <div class="mb-3">

                            @if ($selectedUnit->logo_path)

                                <img
                                    src="{{ asset('storage/' . $selectedUnit->logo_path) }}"
                                    alt="Logo {{ $selectedUnit->name }}"
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

                        @if ($selectedUnit->logo_path)

                            <form
                                method="POST"
                                action="{{ route(
                                    'admin.settings.profile-unit.logo.destroy',
                                    $selectedUnit
                                ) }}"
                                onsubmit="return confirm('Yakin ingin menghapus Logo Unit ini?')"
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


            {{-- Informasi Unit --}}
            <div class="col-lg-8">

                <div class="card card-primary">

                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="bi bi-building me-1"></i>
                            Informasi Unit
                        </h3>
                    </div>

                    <form
                        method="POST"
                        action="{{ route(
                            'admin.settings.profile-unit.update',
                            $selectedUnit
                        ) }}"
                        enctype="multipart/form-data"
                    >
                        @csrf
                        @method('PUT')

                        <div class="card-body">

                            {{-- Nama Unit --}}
                            <div class="mb-3">

                                <label
                                    for="name"
                                    class="form-label"
                                >
                                    Nama Unit
                                </label>

                                <input
                                    type="text"
                                    id="name"
                                    class="form-control"
                                    value="{{ $selectedUnit->name }}"
                                    readonly
                                >

                                <div class="form-text">
                                    Nama Unit dikelola melalui menu
                                    Organisasi dan tidak dapat diubah
                                    dari Profil Unit.
                                </div>

                            </div>


                            {{-- Alamat --}}
                            <div class="mb-3">

                                <label
                                    for="address"
                                    class="form-label"
                                >
                                    Alamat
                                </label>

                                <textarea
                                    id="address"
                                    name="address"
                                    rows="3"
                                    class="form-control @error('address') is-invalid @enderror"
                                >{{ old('address', $selectedUnit->address) }}</textarea>

                                @error('address')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror

                            </div>


                            <div class="row">

                                {{-- Telepon --}}
                                <div class="col-md-6 mb-3">

                                    <label
                                        for="phone"
                                        class="form-label"
                                    >
                                        Telepon
                                    </label>

                                    <input
                                        type="text"
                                        id="phone"
                                        name="phone"
                                        class="form-control @error('phone') is-invalid @enderror"
                                        value="{{ old('phone', $selectedUnit->phone) }}"
                                    >

                                    @error('phone')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                </div>


                                {{-- Email --}}
                                <div class="col-md-6 mb-3">

                                    <label
                                        for="email"
                                        class="form-label"
                                    >
                                        Email
                                    </label>

                                    <input
                                        type="email"
                                        id="email"
                                        name="email"
                                        class="form-control @error('email') is-invalid @enderror"
                                        value="{{ old('email', $selectedUnit->email) }}"
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

                                <label
                                    for="website"
                                    class="form-label"
                                >
                                    Website
                                </label>

                                <input
                                    type="text"
                                    id="website"
                                    name="website"
                                    class="form-control @error('website') is-invalid @enderror"
                                    value="{{ old('website', $selectedUnit->website) }}"
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

                                <label
                                    for="logo"
                                    class="form-label"
                                >
                                    {{ $selectedUnit->logo_path ? 'Ganti Logo' : 'Upload Logo' }}
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
                                    Logo akan otomatis dikompresi dan
                                    disimpan dalam format WebP.
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

    @endif

@stop
