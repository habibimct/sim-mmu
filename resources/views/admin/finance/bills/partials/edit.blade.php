{{-- =========================================================
MODAL EDIT TAGIHAN SISWA
========================================================= --}}

<div
    class="modal fade"
    id="modalEditStudentBill"
    tabindex="-1"
    aria-labelledby="modalEditStudentBillLabel"
    aria-hidden="true"
>

    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content">

            {{-- HEADER --}}

            <div class="modal-header">

                <div>

                    <h5
                        class="modal-title"
                        id="modalEditStudentBillLabel"
                    >
                        Edit Tagihan Siswa
                    </h5>

                    <small class="text-muted">
                        Ubah data tagihan siswa.
                    </small>

                </div>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close"
                ></button>

            </div>


            {{-- FORM --}}

            <form
                method="POST"
                id="formEditStudentBill"
            >

                @csrf

                @method('PUT')


                <div class="modal-body">

                    {{-- =================================================
                    SISWA
                    ================================================== --}}

                    <div class="mb-3">

                        <label class="form-label">
                            Siswa
                        </label>

                        <input
                            type="text"
                            id="edit_bill_student"
                            class="form-control"
                            readonly
                        >

                    </div>


                    {{-- =================================================
                    UNIT
                    ================================================== --}}

                    <div class="mb-3">

                        <label class="form-label">
                            Organisasi / Unit
                        </label>

                        <input
                            type="text"
                            id="edit_bill_organization"
                            class="form-control"
                            readonly
                        >

                    </div>


                    {{-- =================================================
                    TAHUN AKADEMIK
                    ================================================== --}}

                    <div class="mb-3">

                        <label class="form-label">
                            Tahun Akademik
                        </label>

                        <input
                            type="text"
                            id="edit_bill_academic_year"
                            class="form-control"
                            readonly
                        >

                    </div>


                    {{-- =================================================
                    JENIS TAGIHAN
                    ================================================== --}}

                    <div class="mb-3">

                        <label
                            for="edit_bill_type_id"
                            class="form-label"
                        >
                            Jenis Tagihan
                            <span class="text-danger">*</span>
                        </label>

                        <select
                            name="bill_type_id"
                            id="edit_bill_type_id"
                            class="form-select"
                            required
                        >

                            <option value="">
                                -- Pilih Jenis Tagihan --
                            </option>

                            @foreach ($billTypes as $billType)

                                <option
                                    value="{{ $billType->id }}"
                                >
                                    {{ $billType->organization->name }}
                                    —
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
                            for="edit_bill_period"
                            class="form-label"
                        >
                            Periode
                            <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            name="period"
                            id="edit_bill_period"
                            class="form-control"
                            maxlength="50"
                            required
                        >

                    </div>


                    {{-- =================================================
                    NOMINAL
                    ================================================== --}}

                    <div class="mb-3">

                        <label
                            for="edit_bill_amount"
                            class="form-label"
                        >
                            Nominal
                            <span class="text-danger">*</span>
                        </label>

                        <input
                            type="number"
                            name="amount"
                            id="edit_bill_amount"
                            class="form-control"
                            min="0.01"
                            step="0.01"
                            required
                        >

                    </div>


                    {{-- =================================================
                    JATUH TEMPO
                    ================================================== --}}

                    <div class="mb-3">

                        <label
                            for="edit_bill_due_date"
                            class="form-label"
                        >
                            Jatuh Tempo
                        </label>

                        <input
                            type="date"
                            name="due_date"
                            id="edit_bill_due_date"
                            class="form-control"
                        >

                    </div>


                    {{-- =================================================
                    KETERANGAN
                    ================================================== --}}

                    <div class="mb-3">

                        <label
                            for="edit_bill_description"
                            class="form-label"
                        >
                            Keterangan
                        </label>

                        <textarea
                            name="description"
                            id="edit_bill_description"
                            rows="3"
                            maxlength="1000"
                            class="form-control"
                        ></textarea>

                    </div>

                </div>


                {{-- FOOTER --}}

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
                        id="btnUpdateStudentBill"
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
    document.addEventListener('DOMContentLoaded', function () {

        const modal =
            document.getElementById(
                'modalEditStudentBill'
            );

        const form =
            document.getElementById(
                'formEditStudentBill'
            );

        if (!modal || !form) {
            return;
        }


        modal.addEventListener(
            'show.bs.modal',
            function (event) {

                const button =
                    event.relatedTarget;


                /*
                |--------------------------------------------------------------------------
                | URL
                |--------------------------------------------------------------------------
                */

                form.action =
                    button.getAttribute(
                        'data-update-url'
                    );


                /*
                |--------------------------------------------------------------------------
                | Informasi siswa
                |--------------------------------------------------------------------------
                */

                document.getElementById(
                    'edit_bill_student'
                ).value =
                    button.getAttribute(
                        'data-student'
                    );


                document.getElementById(
                    'edit_bill_organization'
                ).value =
                    button.getAttribute(
                        'data-organization'
                    );


                document.getElementById(
                    'edit_bill_academic_year'
                ).value =
                    button.getAttribute(
                        'data-academic-year'
                    );


                /*
                |--------------------------------------------------------------------------
                | Data tagihan
                |--------------------------------------------------------------------------
                */

                document.getElementById(
                    'edit_bill_type_id'
                ).value =
                    button.getAttribute(
                        'data-bill-type-id'
                    );


                document.getElementById(
                    'edit_bill_period'
                ).value =
                    button.getAttribute(
                        'data-period'
                    );


                document.getElementById(
                    'edit_bill_amount'
                ).value =
                    button.getAttribute(
                        'data-amount'
                    );


                document.getElementById(
                    'edit_bill_due_date'
                ).value =
                    button.getAttribute(
                        'data-due-date'
                    );


                document.getElementById(
                    'edit_bill_description'
                ).value =
                    button.getAttribute(
                        'data-description'
                    );

            }
        );


        /*
        |--------------------------------------------------------------------------
        | Cegah double click
        |--------------------------------------------------------------------------
        */

        form.addEventListener(
            'submit',
            function () {

                const button =
                    document.getElementById(
                        'btnUpdateStudentBill'
                    );

                if (!button) {
                    return;
                }

                if (button.disabled) {
                    return;
                }

                button.disabled = true;

                button.innerHTML = `
                    <span
                        class="spinner-border spinner-border-sm me-1"
                        role="status"
                        aria-hidden="true"
                    ></span>
                    Menyimpan...
                `;

            }
        );

    });
</script>

@endpush
