@extends('adminlte::page')

@section('title', 'Naik Kelas')

@section('content_header')

    <div class="d-flex justify-content-between align-items-center">

        <h1>
            <i class="bi bi-arrow-up-circle me-1"></i>
            Naik Kelas
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
    ERROR
    =========================================================== --}}

    @if ($errors->any())

        <div class="alert alert-danger">

            <strong>
                Terdapat kesalahan:
            </strong>

            <ul class="mb-0 mt-2">

                @foreach ($errors->all() as $error)
                    <li>
                        {{ $error }}
                    </li>
                @endforeach

            </ul>

        </div>

    @endif


    {{-- ==========================================================
    FILTER
    =========================================================== --}}

    <div class="card">

        <div class="card-header">
            <h3 class="card-title">
                <i class="bi bi-funnel me-1"></i>
                Tentukan Tahun Ajaran
            </h3>
        </div>

        <div class="card-body">

            <form method="GET" action="{{ route('admin.students.promotion.index') }}" class="row g-3">

                {{-- Tahun Asal --}}
                <div class="col-md-4">

                    <label class="form-label">
                        Tahun Ajaran Asal
                    </label>

                    <select name="source_academic_year_id" class="form-select" required>

                        <option value="">
                            -- Pilih Tahun Ajaran --
                        </option>

                        @foreach ($academicYears as $academicYear)
                            <option value="{{ $academicYear->id }}" @selected($sourceAcademicYearId == $academicYear->id)>
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


                {{-- Kelas Asal --}}
                <div class="col-md-4">

                    <label class="form-label">
                        Kelas Asal
                    </label>

                    <select name="source_class_id" class="form-select">

                        <option value="">
                            -- Pilih Kelas Asal --
                        </option>

                        @foreach ($sourceClasses as $class)
                            <option value="{{ $class->id }}" @selected($sourceClassId == $class->id)>
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

                        Tampilkan

                    </button>

                </div>

            </form>

        </div>

    </div>


    {{-- ==========================================================
    AREA NAIK KELAS
    =========================================================== --}}

    @if ($sourceClass)

        <div class="row">

            {{-- ======================================================
        KELAS ASAL
        ======================================================= --}}

            <div class="col-md-6">

                <div class="card h-100">

                    <div class="card-header">

                        <h3 class="card-title">

                            <i class="bi bi-arrow-up-right me-1"></i>

                            Tahun Ajaran Asal

                        </h3>

                    </div>


                    <div class="card-body">

                        <div class="mb-3">

                            <label class="form-label">
                                Kelas Asal
                            </label>

                            <select id="sourceClassSelect" class="form-select">

                                @foreach ($sourceClasses as $class)
                                    <option value="{{ $class->id }}" @selected($sourceClassId == $class->id)>

                                        Tingkat
                                        {{ $class->level }}

                                        -
                                        {{ $class->name }}

                                    </option>
                                @endforeach

                            </select>

                        </div>


                        <div class="table-responsive">

                            <table class="table table-bordered table-hover">

                                <thead class="table-light">

                                    <tr>

                                        <th width="45" class="text-center">

                                            <input type="checkbox" id="checkAllPromotionStudents">

                                        </th>

                                        <th width="100">
                                            NIS
                                        </th>

                                        <th>
                                            Nama
                                        </th>

                                    </tr>

                                </thead>


                                <tbody>

                                    @forelse ($sourceStudents as $studentAcademicYear)
                                        <tr>

                                            <td class="text-center">

                                                <input type="checkbox" name="student_ids[]"
                                                    value="{{ $studentAcademicYear->student_id }}"
                                                    class="promotion-student-checkbox" form="formPromotion">

                                            </td>

                                            <td>
                                                {{ $studentAcademicYear->student->nis }}
                                            </td>

                                            <td>
                                                {{ $studentAcademicYear->student->name }}
                                            </td>

                                        </tr>

                                    @empty

                                        <tr>

                                            <td colspan="3" class="text-center text-muted py-4">

                                                Tidak ada siswa aktif
                                                pada kelas ini.

                                            </td>

                                        </tr>
                                    @endforelse

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

            </div>


            {{-- ======================================================
        KELAS TUJUAN
        ======================================================= --}}

            <div class="col-md-6">

                <div class="card h-100">

                    <div class="card-header">

                        <h3 class="card-title">

                            <i class="bi bi-arrow-up-circle me-1"></i>

                            Tahun Ajaran Tujuan

                        </h3>

                    </div>


                    <div class="card-body">

                        @if ($targetAcademicYear)

                            <div class="mb-3">

                                <label class="form-label">
                                    Tahun Ajaran
                                </label>

                                <input type="text" class="form-control" value="{{ $targetAcademicYear->name }}"
                                    readonly>

                            </div>


                            @if ($sourceClass && !$isFinalLevel)

                                {{-- Dropdown kelas tujuan --}}
                                <div class="col-md-4">

                                    <label class="form-label">
                                        Kelas Tujuan
                                    </label>

                                    <select name="target_class_id" class="form-select">

                                        <option value="">
                                            -- Pilih Kelas Tujuan --
                                        </option>

                                        @foreach ($targetClasses as $class)
                                            <option value="{{ $class->id }}" @selected($targetClassId == $class->id)>
                                                Tingkat {{ $class->level }}
                                                - {{ $class->name }}
                                            </option>
                                        @endforeach

                                    </select>

                                </div>

                            @endif


                            <div class="table-responsive">

                                <table class="table table-bordered table-hover">

                                    <thead class="table-light">

                                        <tr>

                                            <th width="100">
                                                NIS
                                            </th>

                                            <th>
                                                Nama
                                            </th>

                                        </tr>

                                    </thead>


                                    <tbody>

                                        @forelse ($targetStudents
                                                                    as $studentAcademicYear)
                                            <tr>

                                                <td>
                                                    {{ $studentAcademicYear->student->nis }}
                                                </td>

                                                <td>
                                                    {{ $studentAcademicYear->student->name }}
                                                </td>

                                            </tr>

                                        @empty

                                            <tr>

                                                <td colspan="2" class="text-center text-muted py-4">

                                                    Belum ada siswa
                                                    pada kelas tujuan.

                                                </td>

                                            </tr>
                                        @endforelse

                                    </tbody>

                                </table>

                            </div>
                        @else
                            <div class="alert alert-warning">

                                <i class="bi bi-exclamation-triangle me-1"></i>

                                Tahun ajaran tujuan belum tersedia.

                            </div>

                        @endif

                    </div>

                </div>

            </div>

        </div>


        @if ($sourceClass && $isFinalLevel)
            <div class="alert alert-success mt-3">

                <i class="fas fa-graduation-cap me-1"></i>

                <strong>
                    Tingkat terakhir
                </strong>

                <br>

                Kelas
                <strong>
                    {{ $sourceClass->name }}
                </strong>
                merupakan tingkat terakhir pada unit
                <strong>
                    {{ $sourceClass->organization->name ?? '' }}
                </strong>.

                <br>

                Siswa yang dinyatakan lulus akan dicatat sebagai
                <strong>
                    Alumni
                </strong>
                unit tersebut.

            </div>
        @endif



        {{-- ======================================================
    FORM NAIK KELAS
    ======================================================= --}}

        <form method="POST" action="{{ route('admin.students.promotion.store') }}" id="formPromotion" class="mt-3">

            @csrf

            <input type="hidden" name="source_academic_year_id" value="{{ $sourceAcademicYearId }}">

            <input type="hidden" name="organization_id" value="{{ $organizationId }}">

            <input type="hidden" name="source_class_id" value="{{ $sourceClassId }}">

            <input type="hidden" name="target_class_id" value="{{ $targetClassId }}">


            <div class="text-center">

                @if ($sourceClass && $isFinalLevel)
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-graduation-cap"></i>
                        Luluskan Siswa
                    </button>
                @else
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-arrow-up"></i>
                        Proses Naik Kelas
                    </button>
                @endif

            </div>

        </form>

    @endif

@stop


@section('js')

    <script>
        const checkAll =
            document.getElementById(
                'checkAllPromotionStudents'
            );

        if (checkAll) {

            checkAll.addEventListener(
                'change',
                function() {

                    document
                        .querySelectorAll(
                            '.promotion-student-checkbox'
                        )
                        .forEach(function(checkbox) {

                            checkbox.checked =
                                checkAll.checked;

                        });

                }
            );

        }


        /*
        |--------------------------------------------------------------------------
        | Ganti kelas asal
        |--------------------------------------------------------------------------
        |
        | Saat kelas asal berubah, kita reload halaman
        | dengan parameter baru.
        |--------------------------------------------------------------------------
        */

        const sourceClassSelect =
            document.getElementById(
                'sourceClassSelect'
            );

        if (sourceClassSelect) {

            sourceClassSelect.addEventListener(
                'change',
                function() {

                    const url =
                        new URL(
                            window.location.href
                        );

                    url.searchParams.set(
                        'source_class_id',
                        this.value
                    );

                    url.searchParams.delete(
                        'target_class_id'
                    );

                    window.location.href =
                        url.toString();

                }
            );

        }


        /*
        |--------------------------------------------------------------------------
        | Ganti kelas tujuan
        |--------------------------------------------------------------------------
        */

        const targetClassSelect =
            document.getElementById(
                'targetClassSelect'
            );

        if (targetClassSelect) {

            targetClassSelect.addEventListener(
                'change',
                function() {

                    const url =
                        new URL(
                            window.location.href
                        );

                    url.searchParams.set(
                        'target_class_id',
                        this.value
                    );

                    window.location.href =
                        url.toString();

                }
            );

        }
    </script>

@stop
