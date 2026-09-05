<script>
    document.addEventListener('DOMContentLoaded', function() {

        /*
        |--------------------------------------------------------------------------
        | Modal Edit
        |--------------------------------------------------------------------------
        */

        const editModal =
            document.getElementById('modalEditSubject');

        const editForm =
            document.getElementById('formEditSubject');

        const editCode =
            document.getElementById('editSubjectCode');

        const editName =
            document.getElementById('editSubjectName');

        const editActive =
            document.getElementById('editSubjectActive');


        if (editModal) {

            editModal.addEventListener(
                'show.bs.modal',
                function(event) {

                    const button =
                        event.relatedTarget;

                    const id =
                        button.getAttribute('data-id');

                    const code =
                        button.getAttribute('data-code');

                    const name =
                        button.getAttribute('data-name');

                    const active =
                        button.getAttribute('data-active');


                    editForm.action =
                        `/admin/subjects/${id}`;

                    editCode.value =
                        code ?? '';

                    editName.value =
                        name ?? '';

                    editActive.checked =
                        active === '1';

                }
            );

        }


        /*
        |--------------------------------------------------------------------------
        | Modal Delete
        |--------------------------------------------------------------------------
        */

        const deleteModal =
            document.getElementById(
                'modalDeleteSubject'
            );

        const deleteForm =
            document.getElementById(
                'formDeleteSubject'
            );

        const deleteName =
            document.getElementById(
                'deleteSubjectName'
            );


        if (deleteModal) {

            deleteModal.addEventListener(
                'show.bs.modal',
                function(event) {

                    const button =
                        event.relatedTarget;

                    const id =
                        button.getAttribute(
                            'data-id'
                        );

                    const name =
                        button.getAttribute(
                            'data-name'
                        );


                    deleteForm.action =
                        `/admin/subjects/${id}`;

                    deleteName.textContent =
                        name ?? '';

                }
            );

        }

    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        /*
        |--------------------------------------------------------------------------
        | Buka kembali modal Tambah setelah validasi gagal
        |--------------------------------------------------------------------------
        */

        @if (session('open_subject_modal'))

            const createModal =
                document.getElementById('modalCreateSubject');

            if (createModal) {

                const modal =
                    new bootstrap.Modal(createModal);

                modal.show();

            }
        @endif


        /*
        |--------------------------------------------------------------------------
        | Buka kembali modal Edit setelah validasi gagal
        |--------------------------------------------------------------------------
        */

        @if (session('edit_subject_id'))

            const editModal =
                document.getElementById('modalEditSubject');

            if (editModal) {

                const editButton =
                    document.querySelector(
                        '[data-bs-target="#modalEditSubject"][data-id="{{ session('edit_subject_id') }}"]'
                    );

                if (editButton) {

                    const modal =
                        new bootstrap.Modal(editModal);

                    /*
                    |--------------------------------------------------------------
                    | Isi kembali data dari tombol edit
                    |--------------------------------------------------------------
                    */

                    const id =
                        editButton.getAttribute('data-id');

                    const code =
                        editButton.getAttribute('data-code');

                    const name =
                        editButton.getAttribute('data-name');

                    const active =
                        editButton.getAttribute('data-active');


                    document.getElementById(
                            'editSubjectCode'
                        ).value =
                        @json(old('code', '')) || code || '';

                    document.getElementById(
                            'editSubjectName'
                        ).value =
                        @json(old('name', '')) || name || '';


                    document.getElementById(
                            'editSubjectActive'
                        ).checked =
                        @json(old('is_active', null)) !== null ?
                        @json((bool) old('is_active')) :
                        active === '1';


                    document.getElementById(
                            'formEditSubject'
                        ).action =
                        `/admin/subjects/${id}`;


                    modal.show();

                }

            }
        @endif

    });
</script>
