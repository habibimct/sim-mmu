@extends('adminlte::page')

@section('title', 'Penempatan Siswa')

@section('content_header')

    <div class="d-flex justify-content-between align-items-center">

        <h1>
            <i class="bi bi-person-lines-fill me-1"></i>
            Penempatan Siswa
        </h1>

        <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>
            Kembali
        </a>

    </div>

@stop


@section('content')

    {{-- ==========================================================
    ALERT SUKSES
    =========================================================== --}}

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">

            <i class="bi bi-check-circle me-1"></i>

            {{ session('success') }}

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>

        </div>
    @endif


    {{-- ==========================================================
    FILTER PENEMPATAN
    =========================================================== --}}

    <div class="card">

        <div class="card-header">

            <h3 class="card-title">

                <i class="bi bi-funnel me-1"></i>

                Tentukan Penempatan

            </h3>

        </div>


        <div class="card-body">

            <form method="GET" action="{{ route('admin.students.placement.index') }}" class="row g-3">

                {{-- Tahun Ajaran --}}
                <div class="col-md-4">

                    <label class="form-label">
                        Tahun Ajaran
                        <span class="text-danger">*</span>
                    </label>

                    <select name="academic_year_id" class="form-select" required>

                        <option value="">
                            -- Pilih Tahun Ajaran --
                        </option>

                        @foreach ($academicYears as $academicYear)
                            <option value="{{ $academicYear->id }}" @selected($academicYearId == $academicYear->id)>

                                {{ $academicYear->name }}

                                @if (!$academicYear->is_active)
                                    (Ditutup)
                                @endif

                            </option>
                        @endforeach

                    </select>

                </div>


                {{-- Unit --}}
                <div class="col-md-4">

                    <label class="form-label">
                        Unit
                        <span class="text-danger">*</span>
                    </label>

                    <select name="organization_id" class="form-select" required>

                        <option value="">
                            -- Pilih Unit --
                        </option>

                        @foreach ($organizations as $organization)
                            <option value="{{ $organization->id }}" @selected($organizationId == $organization->id)>

                                {{ $organization->name }}

                            </option>
                        @endforeach

                    </select>

                </div>


                {{-- Kelas --}}
                <div class="col-md-4">

                    <label class="form-label">
                        Kelas
                        <span class="text-danger">*</span>
                    </label>

                    <select name="school_class_id" class="form-select">

                        <option value="">
                            -- Pilih Kelas --
                        </option>

                        @foreach ($classes as $class)
                            <option value="{{ $class->id }}" @selected($schoolClassId == $class->id)>

                                Tingkat {{ $class->level }}
                                - {{ $class->name }}

                            </option>
                        @endforeach

                    </select>

                </div>


                {{-- Tombol --}}
                <div class="col-12">

                    <button type="submit" class="btn btn-primary">

                        <i class="bi bi-search me-1"></i>

                        Tampilkan Siswa

                    </button>

                </div>

            </form>

        </div>

    </div>


    {{-- ==========================================================
DAFTAR SISWA + KELAS TUJUAN
========================================================== --}}

    @if ($schoolClassId)

        <form method="POST" action="{{ route('admin.students.placement.store') }}">

            @csrf

            <input type="hidden" name="academic_year_id" value="{{ $academicYearId }}">

            <input type="hidden" name="organization_id" value="{{ $organizationId }}">

            <input type="hidden" name="school_class_id" value="{{ $schoolClassId }}">

            <div class="row g-3">

                {{-- ==================================================
            SISWA BELUM DITEMPATKAN
            ================================================== --}}

                <div class="col-lg-6">

                    <div class="card h-100">

                        <div class="card-header">

                            <h3 class="card-title">
                                <i class="bi bi-person-plus me-1"></i>
                                Siswa Belum Ditempatkan
                            </h3>

                        </div>

                        <div class="card-body p-0">

                            <div class="table-responsive">

                                <table class="table table-hover table-bordered mb-0">

                                    <thead class="table-light">

                                        <tr>

                                            <th width="50" class="text-center">
                                                <input type="checkbox" id="checkAllStudents">
                                            </th>

                                            <th width="120">
                                                NIS
                                            </th>

                                            <th>
                                                Nama
                                            </th>

                                            <th width="120">
                                                Jenis Kelamin
                                            </th>

                                        </tr>

                                    </thead>

                                    <tbody>

                                        @forelse ($students as $student)
                                            <tr>

                                                <td class="text-center">

                                                    <input type="checkbox" name="student_ids[]" value="{{ $student->id }}"
                                                        class="student-checkbox">

                                                </td>

                                                <td>
                                                    {{ $student->nis }}
                                                </td>

                                                <td>
                                                    {{ $student->name }}
                                                </td>

                                                <td>
                                                    {{ $student->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}
                                                </td>

                                            </tr>

                                        @empty

                                            <tr>

                                                <td colspan="4" class="text-center text-muted py-4">

                                                    Tidak ada siswa yang belum
                                                    ditempatkan.

                                                </td>

                                            </tr>
                                        @endforelse

                                    </tbody>

                                </table>

                            </div>

                        </div>

                        @if ($students->count())
                            <div class="card-footer text-end">

                                <button type="submit" class="btn btn-primary">

                                    <i class="bi bi-person-plus me-1"></i>

                                    Tempatkan Siswa Terpilih

                                </button>

                            </div>
                        @endif

                    </div>

                </div>


                {{-- ==================================================
            SISWA YANG SUDAH ADA DI KELAS
            ================================================== --}}

                <div class="col-lg-6">

                    <div class="card h-100">

                        <div class="card-header">

                            <h3 class="card-title">

                                <i class="bi bi-people me-1"></i>

                                Siswa di Kelas

                                @if ($selectedClass)
                                    <span class="fw-bold">
                                        {{ $selectedClass->name }}
                                    </span>
                                @endif

                            </h3>

                        </div>

                        <div class="card-body p-0">

                            <div class="table-responsive">

                                <table class="table table-hover table-bordered mb-0">

                                    <thead class="table-light">

                                        <tr>

                                            <th width="60">
                                                No.
                                            </th>

                                            <th width="120">
                                                NIS
                                            </th>

                                            <th>
                                                Nama
                                            </th>

                                            <th width="120">
                                                Jenis Kelamin
                                            </th>

                                        </tr>

                                    </thead>

                                    <tbody>

                                        @forelse ($placedStudents as $index => $item)
                                            <tr>

                                                <td>
                                                    {{ $index + 1 }}
                                                </td>

                                                <td>
                                                    {{ $item->student->nis }}
                                                </td>

                                                <td>
                                                    {{ $item->student->name }}
                                                </td>

                                                <td>
                                                    {{ $item->student->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}
                                                </td>

                                            </tr>

                                        @empty

                                            <tr>

                                                <td colspan="4" class="text-center text-muted py-4">

                                                    <i class="bi bi-info-circle me-1"></i>

                                                    Belum ada siswa di kelas ini.

                                                </td>

                                            </tr>
                                        @endforelse

                                    </tbody>

                                </table>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </form>

    @endif

@stop


@section('js')

    <script>
        document
            .getElementById('checkAllStudents')
            ?.addEventListener('change', function() {

                document
                    .querySelectorAll('.student-checkbox')
                    .forEach(function(checkbox) {

                        checkbox.checked =
                            this.checked;

                    }, this);

            });
    </script>

@stop
