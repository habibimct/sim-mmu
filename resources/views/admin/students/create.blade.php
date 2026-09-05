@extends('adminlte::page')

@section('title', 'Tambah Siswa')

@section('content_header')

    <div>
        <h1 class="mb-1">
            Tambah Siswa
        </h1>

        <p class="text-muted mb-0">
            Tambahkan data siswa dan penempatan akademiknya.
        </p>
    </div>

@stop


@section('content')

    <div class="card">

        <div class="card-header">
            <h5 class="mb-0">
                Data Siswa
            </h5>
        </div>


        <form
            method="POST"
            action="{{ route('admin.students.store') }}"
        >

            @csrf


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

                    {{-- ==================================================
                    DATA SISWA
                    =================================================== --}}

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
                                        old('organization_id') == $organization->id
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
                            value="{{ old('nis') }}"
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
                            value="{{ old('name') }}"
                            class="form-control"
                            required
                        >

                        @error('name')

                            <div class="text-danger small mt-1">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>


                    {{-- Jenis Kelamin --}}

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
                                @selected(old('gender') === 'L')
                            >
                                Laki-laki
                            </option>

                            <option
                                value="P"
                                @selected(old('gender') === 'P')
                            >
                                Perempuan
                            </option>

                        </select>

                        @error('gender')

                            <div class="text-danger small mt-1">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>


                    {{-- Tempat lahir --}}

                    <div class="col-md-6">

                        <label class="form-label">
                            Tempat Lahir
                        </label>

                        <input
                            type="text"
                            name="birth_place"
                            value="{{ old('birth_place') }}"
                            class="form-control"
                            maxlength="100"
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
                            value="{{ old('birth_date') }}"
                            class="form-control"
                        >

                    </div>


                    {{-- ==================================================
                    AKADEMIK
                    =================================================== --}}

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
                                        old('academic_year_id') == $academicYear->id
                                    )
                                >
                                    {{ $academicYear->name }}
                                </option>

                            @endforeach

                        </select>

                        @error('academic_year_id')

                            <div class="text-danger small mt-1">
                                {{ $message }}
                            </div>

                        @enderror

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
                                        old('school_class_id') == $schoolClass->id
                                    )
                                >
                                    {{ $schoolClass->name }}
                                </option>

                            @endforeach

                        </select>

                        @error('school_class_id')

                            <div class="text-danger small mt-1">
                                {{ $message }}
                            </div>

                        @enderror

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
                    Simpan Siswa
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


            /*
            | Pastikan pilihan lama tidak tersisa
            | jika tidak lagi sesuai.
            */

            const selected =
                schoolClass.options[
                    schoolClass.selectedIndex
                ];


            if (
                selected
                &&
                selected.value
                &&
                (
                    selected.hidden
                )
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
