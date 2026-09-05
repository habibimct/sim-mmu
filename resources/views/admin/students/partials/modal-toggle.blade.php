{{-- ============================================================
MODAL KONFIRMASI AKTIF / NONAKTIF
============================================================= --}}

<div
    class="modal fade"
    id="toggleStudentModal"
    tabindex="-1"
    aria-labelledby="toggleStudentModalLabel"
    aria-hidden="true"
>

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            {{-- ==================================================
            HEADER
            =================================================== --}}

            <div class="modal-header">

                <h5
                    class="modal-title"
                    id="toggleStudentModalLabel"
                >
                    Konfirmasi Status Siswa
                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Tutup"
                ></button>

            </div>


            {{-- ==================================================
            BODY
            =================================================== --}}

            <div class="modal-body">

                <p class="mb-2">

                    Apakah Anda yakin ingin

                    <strong id="toggleActionText"></strong>

                    siswa berikut?

                </p>


                <div class="alert alert-light border mb-0">

                    <i class="bi bi-person me-1"></i>

                    <strong id="toggleStudentName"></strong>

                </div>

            </div>


            {{-- ==================================================
            FOOTER
            =================================================== --}}

            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-secondary"
                    data-bs-dismiss="modal"
                >
                    <i class="bi bi-x-lg me-1"></i>
                    Batal
                </button>


                <form
                    id="toggleStudentForm"
                    method="POST"
                    class="d-inline"
                >

                    @csrf

                    @method('PATCH')


                    <button
                        type="submit"
                        id="toggleConfirmButton"
                        class="btn"
                    >

                        <i
                            id="toggleConfirmIcon"
                            class="me-1"
                        ></i>

                        <span
                            id="toggleConfirmText"
                        ></span>

                    </button>

                </form>

            </div>

        </div>

    </div>

</div>


{{-- ============================================================
JAVASCRIPT
============================================================= --}}

@push('js')

<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {

        const modalElement =
            document.getElementById(
                'toggleStudentModal'
            );

        if (!modalElement) {
            return;
        }


        const form =
            document.getElementById(
                'toggleStudentForm'
            );

        const actionText =
            document.getElementById(
                'toggleActionText'
            );

        const studentName =
            document.getElementById(
                'toggleStudentName'
            );

        const confirmButton =
            document.getElementById(
                'toggleConfirmButton'
            );

        const confirmIcon =
            document.getElementById(
                'toggleConfirmIcon'
            );

        const confirmText =
            document.getElementById(
                'toggleConfirmText'
            );


        /*
        |--------------------------------------------------------------------------
        | Tombol Aktif / Nonaktif
        |--------------------------------------------------------------------------
        */

        document
            .querySelectorAll(
                '[data-bs-target="#toggleStudentModal"]'
            )
            .forEach(
                function (button) {

                    button.addEventListener(
                        'click',
                        function () {

                            const studentId =
                                this.dataset.studentId;

                            const name =
                                this.dataset.studentName;

                            const isActive =
                                this.dataset.studentActive
                                === '1';


                            /*
                            |--------------------------------------------------------------------------
                            | Nama siswa
                            |--------------------------------------------------------------------------
                            */

                            studentName.textContent =
                                name;


                            /*
                            |--------------------------------------------------------------------------
                            | Action form
                            |--------------------------------------------------------------------------
                            */

                            form.action =
                                "{{ url('admin/students') }}"
                                + "/"
                                + studentId
                                + "/toggle-status";


                            /*
                            |--------------------------------------------------------------------------
                            | Jika siswa aktif
                            |--------------------------------------------------------------------------
                            */

                            if (isActive) {

                                actionText.textContent =
                                    'menonaktifkan';

                                confirmButton.className =
                                    'btn btn-danger';

                                confirmIcon.className =
                                    'bi bi-person-x me-1';

                                confirmText.textContent =
                                    'Ya, Nonaktifkan';

                            }


                            /*
                            |--------------------------------------------------------------------------
                            | Jika siswa nonaktif
                            |--------------------------------------------------------------------------
                            */

                            else {

                                actionText.textContent =
                                    'mengaktifkan';

                                confirmButton.className =
                                    'btn btn-success';

                                confirmIcon.className =
                                    'bi bi-person-check me-1';

                                confirmText.textContent =
                                    'Ya, Aktifkan';

                            }

                        }
                    );

                }
            );


        /*
        |--------------------------------------------------------------------------
        | Proteksi double submit
        |--------------------------------------------------------------------------
        */

        form.addEventListener(
            'submit',
            function () {

                confirmButton.disabled =
                    true;


                confirmText.textContent =
                    'Memproses...';

                confirmIcon.className =
                    'spinner-border spinner-border-sm me-1';

            }
        );


        /*
        |--------------------------------------------------------------------------
        | Reset ketika modal ditutup
        |--------------------------------------------------------------------------
        */

        modalElement.addEventListener(
            'hidden.bs.modal',
            function () {

                confirmButton.disabled =
                    false;

                confirmIcon.className =
                    'me-1';

                confirmText.textContent =
                    '';

                actionText.textContent =
                    '';

                studentName.textContent =
                    '';

            }
        );

    }
);

</script>

@endpush
