<script>
    document.addEventListener('DOMContentLoaded', function() {

        /*
        |--------------------------------------------------------------------------
        | MODAL TAMBAH
        |--------------------------------------------------------------------------
        */

        const createAcademicYear =
            document.getElementById('createAcademicYear');

        const createSchoolClassPlaceholder =
            document.getElementById(
                'createSchoolClassPlaceholder'
            );

        const createSchoolClassList =
            document.getElementById(
                'createSchoolClassList'
            );

        const createSchoolClassOptions =
            document.getElementById(
                'createSchoolClassOptions'
            );

        const createSelectAllClasses =
            document.getElementById(
                'createSelectAllClasses'
            );


        if (
            createAcademicYear &&
            createSchoolClassPlaceholder &&
            createSchoolClassList &&
            createSchoolClassOptions
        ) {

            /*
            |--------------------------------------------------------------------------
            | Filter kelas berdasarkan Tahun Ajaran
            |--------------------------------------------------------------------------
            */

            function filterCreateClasses() {

                const yearId =
                    createAcademicYear.value;


                const items =
                    Array.from(
                        createSchoolClassOptions.querySelectorAll(
                            '.create-school-class-item'
                        )
                    );


                let visibleCount = 0;


                items.forEach(function(item) {

                    const itemYear =
                        item.dataset.academicYear;


                    const checkbox =
                        item.querySelector(
                            '.create-school-class-checkbox'
                        );


                    const match =
                        yearId &&
                        itemYear === yearId;


                    if (match) {

                        item.classList.remove('d-none');

                        visibleCount++;

                    } else {

                        item.classList.add('d-none');

                        /*
                        |------------------------------------------------------------------
                        | Jangan biarkan kelas dari tahun lain ikut terkirim
                        |------------------------------------------------------------------
                        */

                        if (checkbox) {
                            checkbox.checked = false;
                        }
                    }

                });


                /*
                |--------------------------------------------------------------------------
                | Tampilan berdasarkan Tahun Ajaran
                |--------------------------------------------------------------------------
                */

                if (!yearId) {

                    createSchoolClassPlaceholder.textContent =
                        'Pilih Tahun Ajaran terlebih dahulu.';

                    createSchoolClassPlaceholder.classList.remove(
                        'd-none'
                    );

                    createSchoolClassList.classList.add(
                        'd-none'
                    );

                } else {

                    createSchoolClassPlaceholder.classList.add(
                        'd-none'
                    );

                    createSchoolClassList.classList.remove(
                        'd-none'
                    );


                    /*
                    |------------------------------------------------------------------
                    | Tidak ada kelas
                    |------------------------------------------------------------------
                    */

                    if (visibleCount === 0) {

                        createSchoolClassPlaceholder.textContent =
                            'Tidak ada kelas pada Tahun Ajaran ini.';

                        createSchoolClassPlaceholder.classList.remove(
                            'd-none'
                        );

                        createSchoolClassList.classList.add(
                            'd-none'
                        );
                    }
                }


                /*
                |--------------------------------------------------------------------------
                | Reset checkbox Pilih Semua
                |--------------------------------------------------------------------------
                */

                if (createSelectAllClasses) {

                    createSelectAllClasses.checked = false;

                    createSelectAllClasses.indeterminate = false;
                }
            }


            /*
            |--------------------------------------------------------------------------
            | Pilih Tahun Ajaran
            |--------------------------------------------------------------------------
            */

            createAcademicYear.addEventListener(
                'change',
                filterCreateClasses
            );


            /*
            |--------------------------------------------------------------------------
            | Pilih Semua Kelas
            |--------------------------------------------------------------------------
            */

            if (createSelectAllClasses) {

                createSelectAllClasses.addEventListener(
                    'change',
                    function() {

                        const yearId =
                            createAcademicYear.value;


                        if (!yearId) {
                            this.checked = false;
                            return;
                        }


                        const visibleCheckboxes =
                            createSchoolClassOptions.querySelectorAll(
                                '.create-school-class-item:not(.d-none) .create-school-class-checkbox'
                            );


                        visibleCheckboxes.forEach(
                            function(checkbox) {

                                checkbox.checked =
                                    createSelectAllClasses.checked;

                            }
                        );
                    }
                );
            }


            /*
            |--------------------------------------------------------------------------
            | Sinkronisasi checkbox "Pilih Semua"
            |--------------------------------------------------------------------------
            */

            createSchoolClassOptions.addEventListener(
                'change',
                function(event) {

                    if (
                        !event.target.classList.contains(
                            'create-school-class-checkbox'
                        )
                    ) {
                        return;
                    }


                    const visibleCheckboxes =
                        Array.from(
                            createSchoolClassOptions.querySelectorAll(
                                '.create-school-class-item:not(.d-none) .create-school-class-checkbox'
                            )
                        );


                    const checkedCount =
                        visibleCheckboxes.filter(
                            function(checkbox) {
                                return checkbox.checked;
                            }
                        ).length;


                    if (createSelectAllClasses) {

                        createSelectAllClasses.checked =
                            visibleCheckboxes.length > 0 &&
                            checkedCount === visibleCheckboxes.length;


                        createSelectAllClasses.indeterminate =
                            checkedCount > 0 &&
                            checkedCount < visibleCheckboxes.length;
                    }
                }
            );


            /*
            |--------------------------------------------------------------------------
            | Kondisi awal
            |--------------------------------------------------------------------------
            */

            filterCreateClasses();
        }


        /*
        |--------------------------------------------------------------------------
        | MODAL EDIT
        |--------------------------------------------------------------------------
        */

        const editModalElement =
            document.getElementById('modalEditTeachingAssignment');

        const editForm =
            document.getElementById('formEditTeachingAssignment');

        const editTeacher =
            document.getElementById('editTeacher');

        const editAcademicYear =
            document.getElementById('editAcademicYear');

        const editSchoolClass =
            document.getElementById('editSchoolClass');

        const editSubject =
            document.getElementById('editSubject');

        const editActive =
            document.getElementById('editActive');


        /*
        |--------------------------------------------------------------------------
        | Saat EDIT MODAL dibuka
        |--------------------------------------------------------------------------
        */

        if (
            editModalElement &&
            editForm &&
            editTeacher &&
            editAcademicYear &&
            editSchoolClass &&
            editSubject &&
            editActive
        ) {

            editModalElement.addEventListener(
                'show.bs.modal',
                function(event) {

                    const button = event.relatedTarget;

                    if (!button) {
                        return;
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Data dari tombol Edit
                    |--------------------------------------------------------------------------
                    */

                    const id =
                        button.getAttribute('data-id');

                    const teacher =
                        button.getAttribute('data-teacher');

                    const schoolClassId =
                        button.getAttribute('data-class');

                    const subject =
                        button.getAttribute('data-subject');

                    const active =
                        button.getAttribute('data-active');


                    /*
                    |--------------------------------------------------------------------------
                    | Action form
                    |--------------------------------------------------------------------------
                    */

                    editForm.action =
                        `/admin/teaching-assignments/${id}`;


                    /*
                    |--------------------------------------------------------------------------
                    | Guru
                    |--------------------------------------------------------------------------
                    */

                    editTeacher.value =
                        teacher ?? '';


                    /*
                    |--------------------------------------------------------------------------
                    | Tahun Ajaran berdasarkan kelas
                    |--------------------------------------------------------------------------
                    */

                    const selectedOption =
                        editSchoolClass.querySelector(
                            `option[value="${schoolClassId}"]`
                        );

                    if (selectedOption) {

                        const yearId =
                            selectedOption.dataset.academicYear ?? '';

                        editAcademicYear.value =
                            yearId;

                    } else {

                        editAcademicYear.value =
                            '';

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Kelas
                    |--------------------------------------------------------------------------
                    */

                    editSchoolClass.value =
                        schoolClassId ?? '';


                    /*
                    |--------------------------------------------------------------------------
                    | Mata Pelajaran
                    |--------------------------------------------------------------------------
                    */

                    editSubject.value =
                        subject ?? '';


                    /*
                    |--------------------------------------------------------------------------
                    | Status
                    |--------------------------------------------------------------------------
                    */

                    editActive.checked =
                        active === '1';

                }
            );
        }

        /*
        |--------------------------------------------------------------------------
        | MODAL HAPUS
        |--------------------------------------------------------------------------
        */

        const deleteModalElement =
            document.getElementById(
                'modalDeleteTeachingAssignment'
            );

        const deleteForm =
            document.getElementById(
                'formDeleteTeachingAssignment'
            );

        const deleteLabel =
            document.getElementById(
                'deleteTeachingAssignmentLabel'
            );


        if (
            deleteModalElement &&
            deleteForm &&
            deleteLabel
        ) {

            deleteModalElement.addEventListener(
                'show.bs.modal',
                function(event) {

                    const button =
                        event.relatedTarget;

                    if (!button) {
                        return;
                    }


                    const id =
                        button.getAttribute('data-id');

                    const label =
                        button.getAttribute('data-label');


                    deleteForm.action =
                        `/admin/teaching-assignments/${id}`;


                    deleteLabel.textContent =
                        label ?? '';

                }
            );
        }


        /*
        |--------------------------------------------------------------------------
        | VALIDASI GAGAL - MODAL TAMBAH
        |--------------------------------------------------------------------------
        */

        @if (session('open_teaching_assignment_modal'))

            const createModalElement =
                document.getElementById(
                    'modalCreateTeachingAssignment'
                );

            if (createModalElement) {

                const createModal =
                    new bootstrap.Modal(
                        createModalElement
                    );

                createModal.show();
            }
        @endif


        /*
        |--------------------------------------------------------------------------
        | VALIDASI GAGAL - MODAL EDIT
        |--------------------------------------------------------------------------
        */

        @if (session('edit_teaching_assignment_id'))

            const editModalElementValidation =
                document.getElementById(
                    'modalEditTeachingAssignment'
                );

            if (editModalElementValidation) {

                const editModal =
                    new bootstrap.Modal(
                        editModalElementValidation
                    );


                /*
                |----------------------------------------------------------------------
                | Ambil old input
                |----------------------------------------------------------------------
                */

                const oldTeacher =
                    @json(old('teacher_id'));

                const oldSchoolClass =
                    @json(old('school_class_id'));

                const oldSubject =
                    @json(old('subject_id'));

                const oldActive =
                    @json(old('is_active'));


                /*
                |----------------------------------------------------------------------
                | Guru
                |----------------------------------------------------------------------
                */

                if (oldTeacher !== null) {

                    editTeacher.value =
                        oldTeacher;
                }


                /*
                |----------------------------------------------------------------------
                | Tentukan Tahun Ajaran dari kelas lama
                |----------------------------------------------------------------------
                */

                if (oldSchoolClass !== null) {

                    const oldClassOption =
                        Array.from(
                            editSchoolClass.options
                        ).find(function(option) {

                            return option.value ===
                                String(oldSchoolClass);

                        });


                    if (oldClassOption) {

                        const oldAcademicYear =
                            oldClassOption.dataset.academicYear ??
                            '';


                        editAcademicYear.value =
                            oldAcademicYear;


                        filterEditClasses(
                            oldAcademicYear
                        );


                        editSchoolClass.value =
                            oldSchoolClass;
                    }
                }


                /*
                |----------------------------------------------------------------------
                | Mata Pelajaran
                |----------------------------------------------------------------------
                */

                if (oldSubject !== null) {

                    editSubject.value =
                        oldSubject;
                }


                /*
                |----------------------------------------------------------------------
                | Status
                |----------------------------------------------------------------------
                */

                if (oldActive !== null) {

                    editActive.checked =
                        Boolean(oldActive);
                }


                /*
                |----------------------------------------------------------------------
                | Form action
                |----------------------------------------------------------------------
                */

                editForm.action =
                    `/admin/teaching-assignments/{{ session('edit_teaching_assignment_id') }}`;


                editModal.show();
            }
        @endif

    });
</script>
