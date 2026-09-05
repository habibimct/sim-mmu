{{-- ============================================================
MODAL FILTER SISWA
============================================================= --}}

<div
    class="modal fade"
    id="studentFilterModal"
    tabindex="-1"
    aria-labelledby="studentFilterModalLabel"
    aria-hidden="true"
>

    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content">

            {{-- ==================================================
            HEADER
            =================================================== --}}

            <div class="modal-header">

                <div>

                    <h5
                        class="modal-title"
                        id="studentFilterModalLabel"
                    >
                        <i class="bi bi-funnel me-2"></i>
                        Filter Data Siswa
                    </h5>

                    <small class="text-muted">
                        Gunakan filter untuk mempersempit data siswa.
                    </small>

                </div>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Tutup"
                ></button>

            </div>


            {{-- ==================================================
            FORM
            =================================================== --}}

            <form
                method="GET"
                action="{{ route('admin.students.index') }}"
                id="studentFilterForm"
            >

                <div class="modal-body">

                    <div class="row g-3">

                        {{-- ==================================================
                        PENCARIAN
                        =================================================== --}}

                        <div class="col-12">

                            <label
                                for="student_filter_search"
                                class="form-label"
                            >
                                Cari Siswa
                            </label>

                            <input
                                type="text"
                                name="search"
                                id="student_filter_search"
                                class="form-control"
                                placeholder="NIS atau nama siswa..."
                                value="{{ request('search') }}"
                            >

                        </div>


                        {{-- ==================================================
                        ORGANISASI
                        =================================================== --}}

                        <div class="col-md-6">

                            <label
                                for="student_filter_organization_id"
                                class="form-label"
                            >
                                Organisasi
                            </label>

                            <select
                                name="organization_id"
                                id="student_filter_organization_id"
                                class="form-select"
                            >

                                <option value="">
                                    Semua Organisasi
                                </option>

                                @foreach ($organizations as $organization)

                                    <option
                                        value="{{ $organization->id }}"
                                        @selected(
                                            request('organization_id')
                                            == $organization->id
                                        )
                                    >
                                        {{ $organization->name }}
                                    </option>

                                @endforeach

                            </select>

                        </div>


                        {{-- ==================================================
                        TAHUN AKADEMIK
                        =================================================== --}}

                        <div class="col-md-6">

                            <label
                                for="student_filter_academic_year_id"
                                class="form-label"
                            >
                                Tahun Akademik
                            </label>

                            <select
                                name="academic_year_id"
                                id="student_filter_academic_year_id"
                                class="form-select"
                            >

                                <option value="">
                                    Semua Tahun Akademik
                                </option>

                                @foreach ($academicYears as $academicYear)

                                    <option
                                        value="{{ $academicYear->id }}"
                                        @selected(
                                            request('academic_year_id')
                                            == $academicYear->id
                                        )
                                    >
                                        {{ $academicYear->name }}
                                    </option>

                                @endforeach

                            </select>

                        </div>


                        {{-- ==================================================
                        KELAS
                        =================================================== --}}

                        <div class="col-md-6">

                            <label
                                for="student_filter_school_class_id"
                                class="form-label"
                            >
                                Kelas
                            </label>

                            <select
                                name="school_class_id"
                                id="student_filter_school_class_id"
                                class="form-select"
                            >

                                <option value="">
                                    Semua Kelas
                                </option>

                                @foreach ($schoolClasses as $schoolClass)

                                    <option
                                        value="{{ $schoolClass->id }}"
                                        data-organization="{{ $schoolClass->organization_id }}"
                                        data-academic-year="{{ $schoolClass->academic_year_id }}"
                                        @selected(
                                            request('school_class_id')
                                            == $schoolClass->id
                                        )
                                    >
                                        {{ $schoolClass->name }}
                                    </option>

                                @endforeach

                            </select>

                        </div>


                        {{-- ==================================================
                        STATUS
                        =================================================== --}}

                        <div class="col-md-6">

                            <label
                                for="student_filter_status"
                                class="form-label"
                            >
                                Status Siswa
                            </label>

                            <select
                                name="status"
                                id="student_filter_status"
                                class="form-select"
                            >

                                <option value="">
                                    Semua Status
                                </option>

                                <option
                                    value="active"
                                    @selected(
                                        request('status') === 'active'
                                    )
                                >
                                    Aktif
                                </option>

                                <option
                                    value="inactive"
                                    @selected(
                                        request('status') === 'inactive'
                                    )
                                >
                                    Nonaktif
                                </option>

                            </select>

                        </div>

                    </div>

                </div>


                {{-- ==================================================
                FOOTER
                =================================================== --}}

                <div class="modal-footer">

                    <a
                        href="{{ route('admin.students.index') }}"
                        class="btn btn-secondary me-auto"
                    >
                        <i class="bi bi-arrow-counterclockwise me-1"></i>
                        Reset
                    </a>

                    <button
                        type="button"
                        class="btn btn-light"
                        data-bs-dismiss="modal"
                    >
                        Batal
                    </button>

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        <i class="bi bi-funnel me-1"></i>
                        Terapkan Filter
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>


{{-- ============================================================
JAVASCRIPT FILTER
============================================================= --}}

@push('js')

<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {

        const organization =
            document.getElementById(
                'student_filter_organization_id'
            );

        const academicYear =
            document.getElementById(
                'student_filter_academic_year_id'
            );

        const schoolClass =
            document.getElementById(
                'student_filter_school_class_id'
            );


        if (
            !organization ||
            !academicYear ||
            !schoolClass
        ) {
            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Filter kelas
        |--------------------------------------------------------------------------
        */

        function filterClasses() {

            const organizationId =
                organization.value;

            const academicYearId =
                academicYear.value;


            Array.from(
                schoolClass.options
            ).forEach(
                function (option) {

                    /*
                    | Option "Semua Kelas"
                    */

                    if (!option.value) {
                        return;
                    }


                    /*
                    | Jika organisasi kosong,
                    | semua organisasi diperbolehkan.
                    */

                    const matchesOrganization =
                        !organizationId ||
                        option.dataset.organization
                        === organizationId;


                    /*
                    | Jika tahun akademik kosong,
                    | semua tahun diperbolehkan.
                    */

                    const matchesAcademicYear =
                        !academicYearId ||
                        option.dataset.academicYear
                        === academicYearId;


                    option.hidden =
                        !(
                            matchesOrganization &&
                            matchesAcademicYear
                        );

                }
            );


            /*
            |--------------------------------------------------------------------------
            | Pastikan kelas terpilih masih valid
            |--------------------------------------------------------------------------
            */

            const selected =
                schoolClass.options[
                    schoolClass.selectedIndex
                ];


            if (
                selected &&
                selected.value &&
                selected.hidden
            ) {

                schoolClass.value = '';

            }

        }


        /*
        |--------------------------------------------------------------------------
        | Event organisasi
        |--------------------------------------------------------------------------
        */

        organization.addEventListener(
            'change',
            filterClasses
        );


        /*
        |--------------------------------------------------------------------------
        | Event tahun akademik
        |--------------------------------------------------------------------------
        */

        academicYear.addEventListener(
            'change',
            filterClasses
        );


        /*
        |--------------------------------------------------------------------------
        | Jalankan saat halaman dibuka
        |--------------------------------------------------------------------------
        */

        filterClasses();

    }
);

</script>

@endpush
