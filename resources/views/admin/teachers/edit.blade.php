@extends('adminlte::page')

@section('title', 'Edit Guru')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0">Edit Guru</h1>
        <a href="{{ route('admin.teachers.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i>
            Kembali
        </a>
    </div>
@stop

@section('content')


    <div class="card">

        <div class="card-header">
            <h3 class="card-title">
                Edit Data Guru
            </h3>
        </div>

        <form action="{{ route('admin.teachers.update', $teacher) }}" method="POST">

            @csrf
            @method('PUT')

            <div class="card-body">

                {{-- Organisasi / Unit --}}

                <div class="mb-3">
                    <label class="form-label">
                        Organisasi / Unit
                    </label>

                    @php
                        $selectedOrganizations = old('organization_ids', $teacher->organizations->pluck('id')->all());
                    @endphp

                    <div class="border rounded p-3">
                        @foreach ($organizations as $organization)
                            <div class="form-check mb-2">
                                <input type="checkbox" name="organization_ids[]" value="{{ $organization->id }}"
                                    id="organization_{{ $organization->id }}"
                                    class="form-check-input @error('organization_ids') is-invalid @enderror"
                                    @checked(in_array($organization->id, $selectedOrganizations))>

                                <label for="organization_{{ $organization->id }}" class="form-check-label">
                                    {{ $organization->name }}
                                </label>
                            </div>
                        @endforeach
                    </div>

                    @error('organization_ids')
                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>
                    @enderror

                    @error('organization_ids.*')
                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

                {{-- NIK --}}
                <div class="mb-3">
                    <label for="nik" class="form-label">
                        NIK
                    </label>

                    <input type="text" name="nik" id="nik" value="{{ old('nik', $teacher->nik) }}"
                        class="form-control @error('nik') is-invalid @enderror">

                    @error('nik')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                {{-- Nama --}}
                <div class="mb-3">
                    <label for="name" class="form-label">
                        Nama Guru
                    </label>

                    <input type="text" name="name" id="name" value="{{ old('name', $teacher->name) }}"
                        class="form-control @error('name') is-invalid @enderror" required>

                    @error('name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                {{-- Jenis Kelamin --}}
                <div class="mb-3">
                    <label for="gender" class="form-label">
                        Jenis Kelamin
                    </label>

                    <select name="gender" id="gender" class="form-control @error('gender') is-invalid @enderror"
                        required>

                        <option value="">
                            -- Pilih Jenis Kelamin --
                        </option>

                        <option value="male" @selected(old('gender', $teacher->gender) === 'male')>
                            Laki-laki
                        </option>

                        <option value="female" @selected(old('gender', $teacher->gender) === 'female')>
                            Perempuan
                        </option>

                    </select>

                    @error('gender')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                {{-- Tempat Lahir --}}
                <div class="mb-3">
                    <label for="birth_place" class="form-label">
                        Tempat Lahir
                    </label>

                    <input type="text" name="birth_place" id="birth_place"
                        value="{{ old('birth_place', $teacher->birth_place) }}"
                        class="form-control @error('birth_place') is-invalid @enderror">

                    @error('birth_place')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                {{-- Tanggal Lahir --}}
                <div class="mb-3">
                    <label for="birth_date" class="form-label">
                        Tanggal Lahir
                    </label>

                    <input type="date" name="birth_date" id="birth_date"
                        value="{{ old('birth_date', optional($teacher->birth_date)->format('Y-m-d')) }}"
                        class="form-control @error('birth_date') is-invalid @enderror">

                    @error('birth_date')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                {{-- No. HP --}}
                <div class="mb-3">
                    <label for="phone" class="form-label">
                        No. HP
                    </label>

                    <input type="text" name="phone" id="phone" value="{{ old('phone', $teacher->phone) }}"
                        class="form-control @error('phone') is-invalid @enderror">

                    @error('phone')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                {{-- Email --}}
                <div class="mb-3">
                    <label for="email" class="form-label">
                        Email
                    </label>

                    <input type="email" name="email" id="email" value="{{ old('email', $teacher->email) }}"
                        class="form-control @error('email') is-invalid @enderror">

                    @error('email')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                {{-- Status --}}
                <div class="mb-3">
                    <label for="is_active" class="form-label">
                        Status
                    </label>

                    <select name="is_active" id="is_active" class="form-control @error('is_active') is-invalid @enderror">

                        <option value="1" @selected(old('is_active', $teacher->is_active) == '1')>
                            Aktif
                        </option>

                        <option value="0" @selected(old('is_active', $teacher->is_active) == '0')>
                            Nonaktif
                        </option>

                    </select>

                    @error('is_active')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

            </div>

            <div class="card-footer d-flex justify-content-end gap-2">

                <a href="{{ route('admin.teachers.index') }}" class="btn btn-secondary">
                    Batal
                </a>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i>
                    Simpan Perubahan
                </button>

            </div>

        </form>

    </div>


@stop
