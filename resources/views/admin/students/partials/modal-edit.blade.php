@php
    $editStudentClasses = $schoolClasses ?? collect();
@endphp

<div
    class="modal fade"
    id="modalEditStudent"
    tabindex="-1"
    aria-labelledby="modalEditStudentLabel"
    aria-hidden="true"
>
    <div class="modal-dialog modal-lg modal-dialog-scrollable">

        <div class="modal-content">

            <div class="modal-header">

                <div>
                    <h5
                        class="modal-title"
                        id="modalEditStudentLabel"
                    >
                        Edit Siswa
                    </h5>

                    <small class="text-muted">
                        Perbarui data siswa dan penempatan akademiknya.
                    </small>
                </div>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Tutup"
                ></button>

            </div>


            <form
                method="POST"
                id="formEditStudent"
            >

                @csrf
                @method('PUT')


                <div class="modal-body">

                    {{-- Error --}}
                    <div
                        id="editStudentErrors"
                        class="alert alert-danger d-none"
                    >
                        <ul
                            class="mb-0"
                            id="editStudentErrorList"
                        ></ul>
                    </div>


                    <div class="row g-3">

                        {{-- ==================================================
                        DATA SISWA
                        =================================================== --}}

                        <div class="col-12">

                            <h6 class="fw-bold border-bottom pb-2">
                                Data Siswa
                            </h6>

                        </div>


                        {{-- Organisasi --}}

                        <div class="col-md-6">

                            <label
                                for="edit_organization_id"
                                class="form-label"
                            >
                                Organisasi
                            </label>

                            <select
                                name="organization_id"
                                id="edit_organization_id"
                                class="form-select"
                                required
                            >

                                <option value="">
                                    Pilih Organisasi
                                </option>

                                @foreach ($organizations as $organization)

                                    <option
                                        value="{{ $organization->id }}"
                                    >
                                        {{ $organization->name }}
                                    </option>

                                @endforeach

                            </select>

                            <div
                                class="invalid-feedback"
                                data-error-for="organization_id"
                            ></div>

                        </div>


                        {{-- NIS --}}

                        <div class="col-md-6">

                            <label
                                for="edit_nis"
                                class="form-label"
                            >
                                NIS
                            </label>

                            <input
                                type="text"
                                name="nis"
                                id="edit_nis"
                                class="form-control"
                                maxlength="50"
                                required
                            >

                            <div
                                class="invalid-feedback"
                                data-error-for="nis"
                            ></div>

                        </div>


                        {{-- Nama --}}

                        <div class="col-md-8">

                            <label
                                for="edit_name"
                                class="form-label"
                            >
                                Nama Siswa
                            </label>

                            <input
                                type="text"
                                name="name"
                                id="edit_name"
                                class="form-control"
                                required
                            >

                            <div
                                class="invalid-feedback"
                                data-error-for="name"
                            ></div>

                        </div>


                        {{-- Jenis kelamin --}}

                        <div class="col-md-4">

                            <label
                                for="edit_gender"
                                class="form-label"
                            >
                                Jenis Kelamin
                            </label>

                            <select
                                name="gender"
                                id="edit_gender"
                                class="form-select"
                                required
                            >

                                <option value="">
                                    Pilih
                                </option>

                                <option value="L">
                                    Laki-laki
                                </option>

                                <option value="P">
                                    Perempuan
                                </option>

                            </select>

                            <div
                                class="invalid-feedback"
                                data-error-for="gender"
                            ></div>

                        </div>


                        {{-- Tempat lahir --}}

                        <div class="col-md-6">

                            <label
                                for="edit_birth_place"
                                class="form-label"
                            >
                                Tempat Lahir
                            </label>

                            <input
                                type="text"
                                name="birth_place"
                                id="edit_birth_place"
                                class="form-control"
                                maxlength="100"
                            >

                            <div
                                class="invalid-feedback"
                                data-error-for="birth_place"
                            ></div>

                        </div>


                        {{-- Tanggal lahir --}}

                        <div class="col-md-6">

                            <label
                                for="edit_birth_date"
                                class="form-label"
                            >
                                Tanggal Lahir
                            </label>

                            <input
                                type="date"
                                name="birth_date"
                                id="edit_birth_date"
                                class="form-control"
                            >

                            <div
                                class="invalid-feedback"
                                data-error-for="birth_date"
                            ></div>

                        </div>


                        {{-- ==================================================
                        PENEMPATAN AKADEMIK
                        =================================================== --}}

                        <div class="col-12 mt-3">

                            <h6 class="fw-bold border-bottom pb-2">
                                Penempatan Akademik
                            </h6>

                        </div>


                        {{-- Tahun akademik --}}

                        <div class="col-md-6">

                            <label
                                for="edit_academic_year_id"
                                class="form-label"
                            >
                                Tahun Akademik
                            </label>

                            <select
                                name="academic_year_id"
                                id="edit_academic_year_id"
                                class="form-select"
                                required
                            >

                                <option value="">
                                    Pilih Tahun Akademik
                                </option>

                                @foreach ($academicYears as $academicYear)

                                    <option
                                        value="{{ $academicYear->id }}"
                                    >
                                        {{ $academicYear->name }}
                                    </option>

                                @endforeach

                            </select>

                            <div
                                class="invalid-feedback"
                                data-error-for="academic_year_id"
                            ></div>

                        </div>


                        {{-- Kelas --}}

                        <div class="col-md-6">

                            <label
                                for="edit_school_class_id"
                                class="form-label"
                            >
                                Kelas
                            </label>

                            <select
                                name="school_class_id"
                                id="edit_school_class_id"
                                class="form-select"
                                required
                            >

                                <option value="">
                                    Pilih Kelas
                                </option>

                                @foreach ($editStudentClasses as $schoolClass)

                                    <option
                                        value="{{ $schoolClass->id }}"
                                        data-organization="{{ $schoolClass->organization_id }}"
                                        data-academic-year="{{ $schoolClass->academic_year_id }}"
                                    >
                                        {{ $schoolClass->name }}
                                    </option>

                                @endforeach

                            </select>

                            <div
                                class="invalid-feedback"
                                data-error-for="school_class_id"
                            ></div>

                        </div>


                        {{-- Status --}}

                        <div class="col-md-6">

                            <label
                                for="edit_is_active"
                                class="form-label"
                            >
                                Status Siswa
                            </label>

                            <select
                                name="is_active"
                                id="edit_is_active"
                                class="form-select"
                                required
                            >

                                <option value="1">
                                    Aktif
                                </option>

                                <option value="0">
                                    Nonaktif
                                </option>

                            </select>

                            <div
                                class="invalid-feedback"
                                data-error-for="is_active"
                            ></div>

                        </div>

                    </div>

                </div>


                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal"
                    >
                        <i class="bi bi-x-lg me-1"></i>
                        Batal
                    </button>

                    <button
                        type="submit"
                        class="btn btn-primary"
                        id="btnUpdateStudent"
                    >
                        <i class="bi bi-check-lg me-1"></i>
                        Simpan Perubahan
                    </button>

                </div>

            </form>

        </div>

    </div>
</div>


@push('js')

<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {

        const modal =
            document.getElementById(
                'modalEditStudent'
            );

        if (!modal) {
            return;
        }


        const form =
            document.getElementById(
                'formEditStudent'
            );

        const organization =
            document.getElementById(
                'edit_organization_id'
            );

        const academicYear =
            document.getElementById(
                'edit_academic_year_id'
            );

        const schoolClass =
            document.getElementById(
                'edit_school_class_id'
            );

        const submitButton =
            document.getElementById(
                'btnUpdateStudent'
            );

        const errorContainer =
            document.getElementById(
                'editStudentErrors'
            );

        const errorList =
            document.getElementById(
                'editStudentErrorList'
            );


        /*
        |--------------------------------------------------------------------------
        | Filter kelas
        |--------------------------------------------------------------------------
        */

        function filterClasses(
            selectedClassId = ''
        ) {

            const organizationId =
                organization.value;

            const academicYearId =
                academicYear.value;


            let selectedStillValid =
                false;


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


                    const visible =
                        matchesOrganization
                        &&
                        matchesAcademicYear;


                    option.hidden =
                        !visible;


                    if (
                        visible
                        &&
                        option.value ===
                            selectedClassId
                    ) {

                        selectedStillValid =
                            true;

                    }

                }
            );


            if (selectedStillValid) {

                schoolClass.value =
                    selectedClassId;

            } else {

                /*
                | Jangan mempertahankan kelas
                | jika kombinasi unit/tahun berubah.
                */

                schoolClass.value = '';

            }

        }


        /*
        |--------------------------------------------------------------------------
        | Reset error
        |--------------------------------------------------------------------------
        */

        function clearErrors() {

            errorContainer
                .classList
                .add('d-none');

            errorList.innerHTML = '';


            form
                .querySelectorAll(
                    '.is-invalid'
                )
                .forEach(
                    function (element) {

                        element.classList
                            .remove(
                                'is-invalid'
                            );

                    }
                );


            form
                .querySelectorAll(
                    '[data-error-for]'
                )
                .forEach(
                    function (element) {

                        element.textContent =
                            '';

                    }
                );

        }


        /*
        |--------------------------------------------------------------------------
        | Tampilkan validation error
        |--------------------------------------------------------------------------
        */

        function showErrors(
            errors
        ) {

            clearErrors();


            const messages = [];


            Object.keys(errors)
                .forEach(
                    function (field) {

                        const fieldErrors =
                            errors[field];


                        fieldErrors.forEach(
                            function (message) {

                                messages.push(
                                    message
                                );

                            }
                        );


                        const input =
                            form.querySelector(
                                `[name="${field}"]`
                            );


                        if (input) {

                            input.classList
                                .add(
                                    'is-invalid'
                                );

                        }


                        const feedback =
                            form.querySelector(
                                `[data-error-for="${field}"]`
                            );


                        if (
                            feedback
                            &&
                            fieldErrors.length
                        ) {

                            feedback.textContent =
                                fieldErrors[0];

                        }

                    }
                );


            messages.forEach(
                function (message) {

                    const li =
                        document.createElement(
                            'li'
                        );

                    li.textContent =
                        message;

                    errorList.appendChild(
                        li
                    );

                }
            );


            if (messages.length > 0) {

                errorContainer
                    .classList
                    .remove('d-none');

            }

        }


        /*
        |--------------------------------------------------------------------------
        | Tombol Edit
        |--------------------------------------------------------------------------
        */

        document
            .querySelectorAll(
                '.btn-edit-student'
            )
            .forEach(
                function (button) {

                    button.addEventListener(
                        'click',
                        function () {

                            clearErrors();


                            const studentId =
                                button.dataset.studentId;


                            /*
                            |--------------------------------------------------------------------------
                            | Action form
                            |--------------------------------------------------------------------------
                            */

                            form.action =
                                `{{ url('admin/students') }}/${studentId}`;


                            /*
                            |--------------------------------------------------------------------------
                            | Data siswa
                            |--------------------------------------------------------------------------
                            */

                            document
                                .getElementById(
                                    'edit_nis'
                                )
                                .value =
                                button.dataset.studentNis
                                || '';


                            document
                                .getElementById(
                                    'edit_name'
                                )
                                .value =
                                button.dataset.studentName
                                || '';


                            document
                                .getElementById(
                                    'edit_gender'
                                )
                                .value =
                                button.dataset.studentGender
                                || '';


                            document
                                .getElementById(
                                    'edit_birth_place'
                                )
                                .value =
                                button.dataset.studentBirthPlace
                                || '';


                            document
                                .getElementById(
                                    'edit_birth_date'
                                )
                                .value =
                                button.dataset.studentBirthDate
                                || '';


                            document
                                .getElementById(
                                    'edit_is_active'
                                )
                                .value =
                                button.dataset.studentActive
                                || '1';


                            /*
                            |--------------------------------------------------------------------------
                            | Penempatan akademik
                            |--------------------------------------------------------------------------
                            */

                            const organizationId =
                                button.dataset.organizationId
                                || '';


                            const academicYearId =
                                button.dataset.academicYearId
                                || '';


                            const schoolClassId =
                                button.dataset.schoolClassId
                                || '';


                            organization.value =
                                organizationId;


                            academicYear.value =
                                academicYearId;


                            filterClasses(
                                schoolClassId
                            );

                        }
                    );

                }
            );


        /*
        |--------------------------------------------------------------------------
        | Organisasi berubah
        |--------------------------------------------------------------------------
        */

        organization.addEventListener(
            'change',
            function () {

                filterClasses();

            }
        );


        /*
        |--------------------------------------------------------------------------
        | Tahun akademik berubah
        |--------------------------------------------------------------------------
        */

        academicYear.addEventListener(
            'change',
            function () {

                filterClasses();

            }
        );


        /*
        |--------------------------------------------------------------------------
        | Submit
        |--------------------------------------------------------------------------
        */

        form.addEventListener(
            'submit',
            async function (event) {

                event.preventDefault();


                clearErrors();


                submitButton.disabled =
                    true;

                submitButton.innerHTML = `
                    <span
                        class="spinner-border spinner-border-sm me-1"
                        role="status"
                        aria-hidden="true"
                    ></span>
                    Menyimpan...
                `;


                try {

                    const response =
                        await fetch(
                            form.action,
                            {
                                method: 'POST',

                                headers: {
                                    'X-CSRF-TOKEN':
                                        document
                                            .querySelector(
                                                'meta[name="csrf-token"]'
                                            )
                                            .getAttribute(
                                                'content'
                                            ),

                                    'Accept':
                                        'application/json',

                                    'X-Requested-With':
                                        'XMLHttpRequest',
                                },

                                body:
                                    new FormData(form),
                            }
                        );


                    if (
                        response.ok
                    ) {

                        window.location.reload();

                        return;

                    }


                    if (
                        response.status === 422
                    ) {

                        const data =
                            await response.json();

                        showErrors(
                            data.errors
                            || {}
                        );

                        return;

                    }


                    throw new Error(
                        'Terjadi kesalahan saat menyimpan data.'
                    );

                } catch (error) {

                    errorContainer
                        .classList
                        .remove('d-none');

                    errorList.innerHTML = `
                        <li>
                            ${error.message}
                        </li>
                    `;

                } finally {

                    submitButton.disabled =
                        false;

                    submitButton.innerHTML = `
                        <i class="bi bi-check-lg me-1"></i>
                        Simpan Perubahan
                    `;

                }

            }
        );


        /*
        |--------------------------------------------------------------------------
        | Reset ketika modal ditutup
        |--------------------------------------------------------------------------
        */

        modal.addEventListener(
            'hidden.bs.modal',
            function () {

                clearErrors();

                form.reset();

                schoolClass.value = '';

            }
        );

    }
);

</script>

@endpush
