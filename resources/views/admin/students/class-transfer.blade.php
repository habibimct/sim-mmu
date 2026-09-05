@extends('adminlte::page')

@section('title', 'Pindah Kelas')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="m-0">Pindah Kelas</h1>

        <a href="{{ route('admin.students.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i>
            Kembali
        </a>
    </div>
@stop

@section('content')

    {{-- Pesan sukses --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle me-1"></i>
            {{ session('success') }}

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Pesan error --}}
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle me-1"></i>
            {{ session('error') }}

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">

        <div class="card-header">
            <h3 class="card-title">
                Pengaturan Pindah Kelas
            </h3>
        </div>

        <div class="card-body">

            <form method="GET" action="{{ route('admin.students.class-transfer.index') }}" class="row g-3">

                {{-- Tahun Ajaran --}}
                <div class="col-md-4">

                    <label class="form-label">
                        Tahun Ajaran
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

                {{-- Organisasi --}}
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

                    <select name="source_class_id" class="form-select" required>
                        <option value="">
                            -- Pilih Kelas Asal --
                        </option>

                        @foreach ($classes as $class)
                            <option value="{{ $class->id }}" @selected($sourceClassId == $class->id)>
                                Tingkat {{ $class->level }}
                                - {{ $class->name }}
                            </option>
                        @endforeach

                    </select>

                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search"></i>
                        Tampilkan Siswa
                    </button>
                </div>

            </form>

        </div>

    </div>

    {{-- ==========================================================
     AREA PINDAH KELAS
=========================================================== --}}

    @if ($sourceClass)

        <form method="GET" action="{{ route('admin.students.class-transfer.index') }}">

            <input type="hidden" name="academic_year_id" value="{{ $academicYearId }}">

            <input type="hidden" name="organization_id" value="{{ $organizationId }}">

            <input type="hidden" name="source_class_id" value="{{ $sourceClassId }}">


            <div class="row">

                {{-- ==================================================
                 KELAS ASAL
            =================================================== --}}

                <div class="col-md-6">

                    <div class="card h-100">

                        <div class="card-header">

                            <h3 class="card-title">

                                <i class="bi bi-box-arrow-right me-1"></i>

                                Kelas Asal

                            </h3>

                        </div>


                        <div class="card-body">

                            <div class="mb-3">

                                <label class="form-label">
                                    Kelas Asal
                                </label>

                                <select name="source_class_id" class="form-select" onchange="this.form.submit()">

                                    @foreach ($classes as $class)
                                        <option value="{{ $class->id }}" @selected($sourceClassId == $class->id)>

                                            Tingkat {{ $class->level }}
                                            - {{ $class->name }}

                                        </option>
                                    @endforeach

                                </select>

                            </div>


                            <div class="table-responsive">

                                <table class="table table-bordered table-hover">

                                    <thead class="table-light">

                                        <tr>

                                            <th width="45" class="text-center">

                                                <input type="checkbox" id="checkAllStudents">

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

                                        @forelse ($sourceStudents
                                                    as $studentAcademicYear)
                                            <tr>

                                                <td class="text-center">

                                                    <input type="checkbox" name="student_ids[]"
                                                        value="{{ $studentAcademicYear->student_id }}"
                                                        class="student-checkbox" form="formMoveStudents">

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

                                                    Tidak ada siswa
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


                {{-- ==================================================
                 KELAS TUJUAN
            =================================================== --}}

                <div class="col-md-6">

                    <div class="card h-100">

                        <div class="card-header">

                            <h3 class="card-title">

                                <i class="bi bi-box-arrow-in-left me-1"></i>

                                Kelas Tujuan

                            </h3>

                        </div>


                        <div class="card-body">

                            <div class="mb-3">

                                <label class="form-label">
                                    Kelas Tujuan
                                </label>

                                <select name="target_class_id" class="form-select" onchange="this.form.submit()" required>

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

                        </div>

                    </div>

                </div>

            </div>

        </form>


        {{-- ==========================================================
         FORM PROSES PINDAH
    =========================================================== --}}

        <form method="POST" action="{{ route('admin.students.class-transfer.store') }}" id="formMoveStudents"
            class="mt-3">

            @csrf

            <input type="hidden" name="academic_year_id" value="{{ $academicYearId }}">

            <input type="hidden" name="organization_id" value="{{ $organizationId }}">

            <input type="hidden" name="source_class_id" value="{{ $sourceClassId }}">

            <input type="hidden" name="target_class_id" value="{{ $targetClassId }}">


            <div class="text-center">

                <button type="submit" class="btn btn-primary" id="btnMoveStudents" @disabled(!$targetClassId || $sourceStudents->isEmpty())>
                    <i class="bi bi-arrow-left-right me-1"></i>
                    Pindahkan Siswa Terpilih
                </button>

            </div>

        </form>

    @endif

@stop


@section('js')

    <script>
        const checkAllStudents =
            document.getElementById('checkAllStudents');

        if (checkAllStudents) {

            checkAllStudents.addEventListener(
                'change',
                function() {

                    document
                        .querySelectorAll('.student-checkbox')
                        .forEach(function(checkbox) {

                            checkbox.checked =
                                checkAllStudents.checked;

                        });

                }
            );

        }
    </script>

    <script>
        const moveForm = document.getElementById('formMoveStudents');
        const moveButton = document.getElementById('btnMoveStudents');

        if (moveForm && moveButton) {
            moveForm.addEventListener('submit', function(event) {

                const checkedStudents =
                    document.querySelectorAll(
                        '.student-checkbox:checked'
                    );

                if (checkedStudents.length === 0) {
                    event.preventDefault();

                    alert(
                        'Silakan pilih minimal satu siswa yang akan dipindahkan.'
                    );

                    return;
                }

                const confirmed = confirm(
                    'Pindahkan ' +
                    checkedStudents.length +
                    ' siswa ke kelas tujuan?'
                );

                if (!confirmed) {
                    event.preventDefault();
                }
            });
        }
    </script>

@stop
