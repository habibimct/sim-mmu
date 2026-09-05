{{-- =========================================================
MODAL CREATE TAGIHAN SISWA
========================================================= --}}

<div
    class="modal fade"
    id="modalCreateStudentBill"
    tabindex="-1"
    aria-labelledby="modalCreateStudentBillLabel"
    aria-hidden="true"
>
    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content">

            {{-- =====================================================
            HEADER
            ====================================================== --}}

            <div class="modal-header">

                <div>

                    <h5
                        class="modal-title"
                        id="modalCreateStudentBillLabel"
                    >
                        Tambah Tagihan Siswa
                    </h5>

                    <p class="text-muted small mb-0 mt-1">
                        Buat tagihan untuk satu siswa atau beberapa siswa sekaligus.
                    </p>

                </div>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Tutup"
                ></button>

            </div>


            {{-- =====================================================
            FORM
            ====================================================== --}}

            <form
                method="POST"
                action="{{ route('admin.finance.bills.store') }}"
                id="formCreateStudentBill"
            >

                @csrf


                <div class="modal-body">

                    {{-- =================================================
                    MODE
                    ================================================== --}}

                    <div class="mb-4">

                        <label class="form-label fw-semibold">
                            Mode Pembuatan
                        </label>

                        <div class="row g-3">

                            {{-- INDIVIDU --}}

                            <div class="col-md-6">

                                <label
                                    class="border rounded-3 p-3 d-block h-100"
                                    for="create_mode_individual"
                                    style="cursor: pointer;"
                                >

                                    <div class="form-check">

                                        <input
                                            class="form-check-input"
                                            type="radio"
                                            name="create_mode"
                                            id="create_mode_individual"
                                            value="individual"
                                            checked
                                        >

                                        <span class="form-check-label fw-semibold">
                                            Individu
                                        </span>

                                    </div>

                                    <div class="small text-muted mt-1 ms-4">
                                        Membuat tagihan untuk satu siswa.
                                    </div>

                                </label>

                            </div>


                            {{-- MASSAL --}}

                            <div class="col-md-6">

                                <label
                                    class="border rounded-3 p-3 d-block h-100"
                                    for="create_mode_bulk"
                                    style="cursor: pointer;"
                                >

                                    <div class="form-check">

                                        <input
                                            class="form-check-input"
                                            type="radio"
                                            name="create_mode"
                                            id="create_mode_bulk"
                                            value="bulk"
                                        >

                                        <span class="form-check-label fw-semibold">
                                            Massal
                                        </span>

                                    </div>

                                    <div class="small text-muted mt-1 ms-4">
                                        Membuat tagihan untuk banyak siswa sekaligus.
                                    </div>

                                </label>

                            </div>

                        </div>

                    </div>


                    {{-- =================================================
                    MODE INDIVIDU
                    ================================================== --}}

                    <div id="individualBillFields">

                        <div class="mb-3">

                            <label
                                for="student_academic_year_id"
                                class="form-label"
                            >
                                Siswa
                                <span class="text-danger">*</span>
                            </label>

                            <select
                                name="student_academic_year_id"
                                id="student_academic_year_id"
                                class="form-select"
                            >

                                <option value="">
                                    -- Pilih Siswa --
                                </option>

                                @foreach ($studentAcademicYears as $studentAcademicYear)

                                    <option
                                        value="{{ $studentAcademicYear->id }}"
                                        data-organization-id="{{ $studentAcademicYear->organization_id }}"
                                        data-academic-year-id="{{ $studentAcademicYear->academic_year_id }}"
                                    >
                                        {{ $studentAcademicYear->student->nis }}
                                        —
                                        {{ $studentAcademicYear->student->name }}

                                        @if ($studentAcademicYear->schoolClass)
                                            —
                                            {{ $studentAcademicYear->schoolClass->name }}
                                        @endif

                                    </option>

                                @endforeach

                            </select>

                        </div>

                    </div>


                    {{-- =================================================
                    MODE MASSAL
                    ================================================== --}}

                    <div
                        id="bulkBillFields"
                        class="d-none"
                    >

                        {{-- Tahun Akademik --}}

                        <div class="mb-3">

                            <label
                                for="bulk_academic_year_id"
                                class="form-label"
                            >
                                Tahun Akademik
                                <span class="text-danger">*</span>
                            </label>

                            <select
                                name="bulk_academic_year_id"
                                id="bulk_academic_year_id"
                                class="form-select"
                            >

                                <option value="">
                                    -- Pilih Tahun Akademik --
                                </option>

                                @foreach ($academicYears as $academicYear)

                                    <option
                                        value="{{ $academicYear->id }}"
                                    >
                                        {{ $academicYear->name }}
                                    </option>

                                @endforeach

                            </select>

                        </div>


                        {{-- Kelas --}}

                        <div class="mb-3">

                            <label
                                for="bulk_school_class_id"
                                class="form-label"
                            >
                                Kelas
                                <span class="text-danger">*</span>
                            </label>

                            <select
                                name="bulk_school_class_id"
                                id="bulk_school_class_id"
                                class="form-select"
                            >

                                <option value="">
                                    -- Pilih Kelas --
                                </option>

                                @foreach ($schoolClasses as $schoolClass)

                                    <option
                                        value="{{ $schoolClass->id }}"
                                        data-academic-year-id="{{ $schoolClass->academic_year_id }}"
                                    >
                                        {{ $schoolClass->name }}
                                    </option>

                                @endforeach

                            </select>

                            <div class="form-text">
                                Hanya siswa aktif pada kelas yang dipilih
                                yang akan dibuatkan tagihan.
                            </div>

                        </div>

                    </div>


                    {{-- =================================================
                    JENIS TAGIHAN
                    ================================================== --}}

                    <div class="mb-3">

                        <label
                            for="create_bill_type_id"
                            class="form-label"
                        >
                            Jenis Tagihan
                            <span class="text-danger">*</span>
                        </label>

                        <select
                            name="bill_type_id"
                            id="create_bill_type_id"
                            class="form-select"
                            required
                        >

                            <option value="">
                                -- Pilih Jenis Tagihan --
                            </option>

                            @foreach ($billTypes as $billType)

                                <option
                                    value="{{ $billType->id }}"
                                    data-organization-id="{{ $billType->organization_id }}"
                                >
                                    {{ $billType->name }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- =================================================
                    PERIODE
                    ================================================== --}}

                    <div class="mb-3">

                        <label
                            for="create_period"
                            class="form-label"
                        >
                            Periode
                            <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            name="period"
                            id="create_period"
                            class="form-control"
                            maxlength="50"
                            placeholder="Contoh: September 2026"
                            required
                        >

                    </div>


                    {{-- =================================================
                    NOMINAL
                    ================================================== --}}

                    <div class="mb-3">

                        <label
                            for="create_amount"
                            class="form-label"
                        >
                            Nominal
                            <span class="text-danger">*</span>
                        </label>

                        <input
                            type="number"
                            name="amount"
                            id="create_amount"
                            class="form-control"
                            min="0.01"
                            step="0.01"
                            placeholder="500000"
                            required
                        >

                    </div>


                    {{-- =================================================
                    JATUH TEMPO
                    ================================================== --}}

                    <div class="mb-3">

                        <label
                            for="create_due_date"
                            class="form-label"
                        >
                            Jatuh Tempo
                        </label>

                        <input
                            type="date"
                            name="due_date"
                            id="create_due_date"
                            class="form-control"
                        >

                    </div>


                    {{-- =================================================
                    KETERANGAN
                    ================================================== --}}

                    <div class="mb-3">

                        <label
                            for="create_description"
                            class="form-label"
                        >
                            Keterangan
                        </label>

                        <textarea
                            name="description"
                            id="create_description"
                            rows="3"
                            maxlength="1000"
                            class="form-control"
                            placeholder="Keterangan tambahan..."
                        ></textarea>

                    </div>

                </div>


                {{-- =====================================================
                FOOTER
                ====================================================== --}}

                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal"
                    >
                        Batal
                    </button>

                    <button
                        type="submit"
                        class="btn btn-primary"
                        id="btnCreateStudentBill"
                    >
                        <i class="bi bi-check-lg me-1"></i>
                        Buat Tagihan
                    </button>

                </div>

            </form>

        </div>

    </div>
</div>


{{-- =========================================================
JAVASCRIPT CREATE
========================================================= --}}

@push('js')

<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {

        const individualRadio =
            document.getElementById(
                'create_mode_individual'
            );

        const bulkRadio =
            document.getElementById(
                'create_mode_bulk'
            );

        const individualFields =
            document.getElementById(
                'individualBillFields'
            );

        const bulkFields =
            document.getElementById(
                'bulkBillFields'
            );

        const individualStudent =
            document.getElementById(
                'student_academic_year_id'
            );

        const bulkAcademicYear =
            document.getElementById(
                'bulk_academic_year_id'
            );

        const bulkSchoolClass =
            document.getElementById(
                'bulk_school_class_id'
            );

        const form =
            document.getElementById(
                'formCreateStudentBill'
            );

        const submitButton =
            document.getElementById(
                'btnCreateStudentBill'
            );


        if (
            !individualRadio ||
            !bulkRadio ||
            !individualFields ||
            !bulkFields ||
            !form
        ) {
            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Mode Individu / Massal
        |--------------------------------------------------------------------------
        */

        function updateCreateMode() {

            const individual =
                individualRadio.checked;


            if (individual) {

                individualFields.classList.remove(
                    'd-none'
                );

                bulkFields.classList.add(
                    'd-none'
                );


                /*
                | Aktifkan validasi siswa
                */

                if (individualStudent) {

                    individualStudent.required =
                        true;

                }


                /*
                | Matikan validasi massal
                */

                if (bulkAcademicYear) {

                    bulkAcademicYear.required =
                        false;

                }

                if (bulkSchoolClass) {

                    bulkSchoolClass.required =
                        false;

                }

            } else {

                individualFields.classList.add(
                    'd-none'
                );

                bulkFields.classList.remove(
                    'd-none'
                );


                /*
                | Matikan validasi siswa
                */

                if (individualStudent) {

                    individualStudent.required =
                        false;

                }


                /*
                | Aktifkan validasi massal
                */

                if (bulkAcademicYear) {

                    bulkAcademicYear.required =
                        true;

                }

                if (bulkSchoolClass) {

                    bulkSchoolClass.required =
                        true;

                }

            }

        }


        individualRadio.addEventListener(
            'change',
            updateCreateMode
        );


        bulkRadio.addEventListener(
            'change',
            updateCreateMode
        );


        /*
        |--------------------------------------------------------------------------
        | Filter kelas berdasarkan tahun akademik
        |--------------------------------------------------------------------------
        */

        function filterBulkClasses() {

            if (!bulkAcademicYear || !bulkSchoolClass) {
                return;
            }


            const academicYearId =
                bulkAcademicYear.value;


            let selectedVisible =
                false;


            Array.from(
                bulkSchoolClass.options
            ).forEach(function (option) {

                if (!option.value) {

                    option.hidden = false;

                    return;
                }


                const visible =
                    !academicYearId ||
                    option.dataset.academicYearId ===
                    academicYearId;


                option.hidden =
                    !visible;


                if (
                    visible &&
                    option.selected
                ) {

                    selectedVisible =
                        true;

                }

            });


            if (!selectedVisible) {

                bulkSchoolClass.value = '';

            }

        }


        if (bulkAcademicYear) {

            bulkAcademicYear.addEventListener(
                'change',
                filterBulkClasses
            );

        }


        /*
        |--------------------------------------------------------------------------
        | Cegah double click
        |--------------------------------------------------------------------------
        */

        form.addEventListener(
            'submit',
            function () {

                if (!submitButton) {
                    return;
                }

                if (submitButton.disabled) {
                    return;
                }


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

            }
        );


        /*
        |--------------------------------------------------------------------------
        | Reset ketika modal ditutup
        |--------------------------------------------------------------------------
        */

        const modal =
            document.getElementById(
                'modalCreateStudentBill'
            );


        if (modal) {

            modal.addEventListener(
                'hidden.bs.modal',
                function () {

                    form.reset();

                    individualRadio.checked =
                        true;

                    updateCreateMode();

                    filterBulkClasses();


                    if (submitButton) {

                        submitButton.disabled =
                            false;

                        submitButton.innerHTML = `
                            <i class="bi bi-check-lg me-1"></i>
                            Buat Tagihan
                        `;

                    }

                }
            );

        }


        /*
        |--------------------------------------------------------------------------
        | Kondisi awal
        |--------------------------------------------------------------------------
        */

        updateCreateMode();

        filterBulkClasses();

    });

</script>

@endpush
