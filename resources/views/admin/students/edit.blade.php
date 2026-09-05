@php

    $selectedOrganization =
        old(
            'organization_id',
            $studentAcademicYear?->organization_id
                ?? $student->organization_id
        );

    $selectedAcademicYear =
        old(
            'academic_year_id',
            $studentAcademicYear?->academic_year_id
        );

    $selectedSchoolClass =
        old(
            'school_class_id',
            $studentAcademicYear?->school_class_id
        );

@endphp


@extends('adminlte::page')

@section('title', 'Edit Siswa')

@section('content_header')

    <div>

        <h1 class="mb-1">
            Edit Siswa
        </h1>

        <p class="text-muted mb-0">
            Perbarui data siswa dan penempatan akademiknya.
        </p>

    </div>

@stop


@section('content')

    @php

        $selectedOrganization =
            old(
                'organization_id',
                $studentAcademicYear?->organization_id
                    ?? $student->organization_id
            );

        $selectedAcademicYear =
            old(
                'academic_year_id',
                $studentAcademicYear?->academic_year_id
            );

        $selectedSchoolClass =
            old(
                'school_class_id',
                $studentAcademicYear?->school_class_id
            );

    @endphp


    <div class="card">

        <div class="card-header">

            <h5 class="mb-0">
                Data Siswa
            </h5>

        </div>


        <form
            method="POST"
            action="{{ route('admin.students.update', $student) }}"
        >

            @csrf

            @method('PUT')


            <div class="card-body">

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


                <div class="row g-3">

                    {{-- DATA SISWA --}}

                    <div class="col-12">

                        <h6 class="fw-bold border-bottom pb-2">
                            Data Siswa
                        </h6>

                    </div>


                    {{-- Unit --}}

                    <div class="col-md-6">

                        <label class="form-label">
                            Unit
                        </label>

                        <select
                            name="organization_id"
                            id="organization_id"
                            class="form-select"
                            required
                        >

                            <option value="">
                                Pilih Unit
                            </option>

                            @foreach ($organizations as $organization)

                                <option
                                    value="{{ $organization->id }}"
                                    @selected(
                                        $selectedOrganization == $organization->id
                                    )
                                >
                                    {{ $organization->name }}
                                </option>

                            @endforeach

                        </select>

                        @error('organization_id')

                            <div class="text-danger small mt-1">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>


                    {{-- NIS --}}

                    <div class="col-md-6">

                        <label class="form-label">
                            NIS
                        </label>

                        <input
                            type="text"
                            name="nis"
                            value="{{ old('nis', $student->nis) }}"
                            class="form-control"
                            maxlength="50"
                            required
                        >

                        @error('nis')

                            <div class="text-danger small mt-1">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>


                    {{-- Nama --}}

                    <div class="col-md-8">

                        <label class="form-label">
                            Nama Siswa
                        </label>

                        <input
                            type="text"
                            name="name"
                            value="{{ old('name', $student->name) }}"
                            class="form-control"
                            required
                        >

                    </div>


                    {{-- Gender --}}

                    <div class="col-md-4">

                        <label class="form-label">
                            Jenis Kelamin
                        </label>

                        <select
                            name="gender"
                            class="form-select"
                            required
                        >

                            <option value="">
                                Pilih
                            </option>

                            <option
                                value="L"
                                @selected(
                                    old('gender', $student->gender) === 'L'
                                )
                            >
                                Laki-laki
                            </option>

                            <option
                                value="P"
                                @selected(
                                    old('gender', $student->gender) === 'P'
                                )
                            >
                                Perempuan
                            </option>

                        </select>

                    </div>


                    {{-- Tempat lahir --}}

                    <div class="col-md-6">

                        <label class="form-label">
                            Tempat Lahir
                        </label>

                        <input
                            type="text"
                            name="birth_place"
                            value="{{ old('birth_place', $student->birth_place) }}"
                            class="form-control"
                        >

                    </div>


                    {{-- Tanggal lahir --}}

                    <div class="col-md-6">

                        <label class="form-label">
                            Tanggal Lahir
                        </label>

                        <input
                            type="date"
                            name="birth_date"
                            value="{{ old(
                                'birth_date',
                                $student->birth_date?->format('Y-m-d')
                            ) }}"
                            class="form-control"
                        >

                    </div>


                    {{-- AKADEMIK --}}

                    <div class="col-12 mt-4">

                        <h6 class="fw-bold border-bottom pb-2">
                            Penempatan Akademik
                        </h6>

                    </div>


                    {{-- Tahun akademik --}}

                    <div class="col-md-6">

                        <label class="form-label">
                            Tahun Akademik
                        </label>

                        <select
                            name="academic_year_id"
                            id="academic_year_id"
                            class="form-select"
                            required
                        >

                            <option value="">
                                Pilih Tahun Akademik
                            </option>

                            @foreach ($academicYears as $academicYear)

                                <option
                                    value="{{ $academicYear->id }}"
                                    @selected(
                                        $selectedAcademicYear == $academicYear->id
                                    )
                                >
                                    {{ $academicYear->name }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- Kelas --}}

                    <div class="col-md-6">

                        <label class="form-label">
                            Kelas
                        </label>

                        <select
                            name="school_class_id"
                            id="school_class_id"
                            class="form-select"
                            required
                        >

                            <option value="">
                                Pilih Kelas
                            </option>

                            @foreach ($schoolClasses as $schoolClass)

                                <option
                                    value="{{ $schoolClass->id }}"
                                    data-organization="{{ $schoolClass->organization_id }}"
                                    data-academic-year="{{ $schoolClass->academic_year_id }}"
                                    @selected(
                                        $selectedSchoolClass == $schoolClass->id
                                    )
                                >
                                    {{ $schoolClass->name }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- Status --}}

                    <div class="col-md-6">

                        <label class="form-label">
                            Status Siswa
                        </label>

                        <select
                            name="is_active"
                            class="form-select"
                            required
                        >

                            <option
                                value="1"
                                @selected(
                                    old(
                                        'is_active',
                                        $student->is_active ? '1' : '0'
                                    ) == '1'
                                )
                            >
                                Aktif
                            </option>

                            <option
                                value="0"
                                @selected(
                                    old(
                                        'is_active',
                                        $student->is_active ? '1' : '0'
                                    ) == '0'
                                )
                            >
                                Tidak Aktif
                            </option>

                        </select>

                    </div>

                </div>

            </div>


            <div class="card-footer d-flex justify-content-between">

                <a
                    href="{{ route('admin.students.index') }}"
                    class="btn btn-secondary"
                >
                    <i class="bi bi-arrow-left me-1"></i>
                    Kembali
                </a>


                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    <i class="bi bi-check-lg me-1"></i>
                    Simpan Perubahan
                </button>

            </div>

        </form>

    </div>

@stop


@push('js')

<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {

        const organization =
            document.getElementById(
                'organization_id'
            );

        const academicYear =
            document.getElementById(
                'academic_year_id'
            );

        const schoolClass =
            document.getElementById(
                'school_class_id'
            );


        function filterClasses() {

            const organizationId =
                organization.value;

            const academicYearId =
                academicYear.value;


            Array.from(
                schoolClass.options
            ).forEach(
                function (option) {

                    if (!option.value) {
                        return;
                    }

                    const matchesOrganization =
                        option.dataset.organization
                        === organizationId;

                    const matchesAcademicYear =
                        option.dataset.academicYear
                        === academicYearId;

                    option.hidden =
                        !(
                            matchesOrganization
                            &&
                            matchesAcademicYear
                        );

                }
            );


            const selected =
                schoolClass.options[
                    schoolClass.selectedIndex
                ];


            if (
                selected
                &&
                selected.value
                &&
                selected.hidden
            ) {

                schoolClass.value = '';

            }

        }


        organization.addEventListener(
            'change',
            filterClasses
        );

        academicYear.addEventListener(
            'change',
            filterClasses
        );

        filterClasses();

    }
);

</script>

@endpush
