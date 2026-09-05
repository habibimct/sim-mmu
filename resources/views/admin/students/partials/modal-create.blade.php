<div class="modal fade" id="modalCreateStudent" tabindex="-1" aria-labelledby="modalCreateStudentLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">

        <div class="modal-content">

            <div class="modal-header">

                <div>
                    <h5 class="modal-title" id="modalCreateStudentLabel">
                        Tambah Siswa
                    </h5>

                    <small class="text-muted">
                        Tambahkan data siswa dan penempatan akademiknya.
                    </small>
                </div>

                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>

            </div>


            <form method="POST" action="{{ route('admin.students.store', request()->query()) }}"
                id="formCreateStudent">

                @csrf

                <div class="modal-body">

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

                            <label for="create_organization_id" class="form-label">
                                Organisasi
                            </label>

                            <select name="organization_id" id="create_organization_id" class="form-select" required>

                                <option value="">
                                    Pilih Organisasi
                                </option>

                                @foreach ($organizations as $organization)
                                    <option value="{{ $organization->id }}">
                                        {{ $organization->name }}
                                    </option>
                                @endforeach

                            </select>

                        </div>


                        {{-- NIS --}}

                        <div class="col-md-6">

                            <label for="create_nis" class="form-label">
                                NIS
                            </label>

                            <input type="text" name="nis" id="create_nis" class="form-control" maxlength="50"
                                required>

                        </div>


                        {{-- Nama --}}

                        <div class="col-md-8">

                            <label for="create_name" class="form-label">
                                Nama Siswa
                            </label>

                            <input type="text" name="name" id="create_name" class="form-control" maxlength="255"
                                required>

                        </div>


                        {{-- Jenis Kelamin --}}

                        <div class="col-md-4">

                            <label for="create_gender" class="form-label">
                                Jenis Kelamin
                            </label>

                            <select name="gender" id="create_gender" class="form-select" required>

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

                        </div>


                        {{-- Tempat lahir --}}

                        <div class="col-md-6">

                            <label for="create_birth_place" class="form-label">
                                Tempat Lahir
                            </label>

                            <input type="text" name="birth_place" id="create_birth_place" class="form-control"
                                maxlength="100">

                        </div>


                        {{-- Tanggal lahir --}}

                        <div class="col-md-6">

                            <label for="create_birth_date" class="form-label">
                                Tanggal Lahir
                            </label>

                            <input type="date" name="birth_date" id="create_birth_date" class="form-control">

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

                            <label for="create_academic_year_id" class="form-label">
                                Tahun Akademik
                            </label>

                            <select name="academic_year_id" id="create_academic_year_id" class="form-select" required>

                                <option value="">
                                    Pilih Tahun Akademik
                                </option>

                                @foreach ($academicYears as $academicYear)
                                    <option value="{{ $academicYear->id }}">
                                        {{ $academicYear->name }}
                                    </option>
                                @endforeach

                            </select>

                        </div>


                        {{-- Kelas --}}

                        <div class="col-md-6">

                            <label for="create_school_class_id" class="form-label">
                                Kelas
                            </label>

                            <select name="school_class_id" id="create_school_class_id" class="form-select" required>

                                <option value="">
                                    Pilih Kelas
                                </option>

                                @foreach ($schoolClasses as $schoolClass)
                                    <option value="{{ $schoolClass->id }}"
                                        data-organization="{{ $schoolClass->organization_id }}"
                                        data-academic-year="{{ $schoolClass->academic_year_id }}">
                                        {{ $schoolClass->name }}
                                    </option>
                                @endforeach

                            </select>

                        </div>

                    </div>

                </div>


                <div class="modal-footer">

                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg me-1"></i>
                        Batal
                    </button>

                    <button type="submit" class="btn btn-primary" id="btnCreateStudent">
                        <i class="bi bi-check-lg me-1"></i>
                        Simpan Siswa
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
            function() {

                const modal =
                    document.getElementById(
                        'modalCreateStudent'
                    );

                if (!modal) {
                    return;
                }


                const form =
                    document.getElementById(
                        'formCreateStudent'
                    );

                const organization =
                    document.getElementById(
                        'create_organization_id'
                    );

                const academicYear =
                    document.getElementById(
                        'create_academic_year_id'
                    );

                const schoolClass =
                    document.getElementById(
                        'create_school_class_id'
                    );

                const submitButton =
                    document.getElementById(
                        'btnCreateStudent'
                    );


                if (
                    !form ||
                    !organization ||
                    !academicYear ||
                    !schoolClass ||
                    !submitButton
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


                    /*
                    | Reset pilihan kelas
                    */

                    schoolClass.value = '';


                    Array.from(
                        schoolClass.options
                    ).forEach(
                        function(option) {

                            /*
                            | Option default
                            */

                            if (!option.value) {

                                option.hidden = false;
                                option.disabled = false;

                                return;
                            }


                            /*
                            | Cocokkan organisasi
                            */

                            const matchesOrganization =
                                organizationId !== '' &&
                                option.dataset.organization ===
                                organizationId;


                            /*
                            | Cocokkan tahun akademik
                            */

                            const matchesAcademicYear =
                                academicYearId !== '' &&
                                option.dataset.academicYear ===
                                academicYearId;


                            /*
                            | Kelas hanya boleh dipilih jika
                            | organisasi DAN tahun akademik cocok.
                            */

                            const visible =
                                matchesOrganization &&
                                matchesAcademicYear;


                            option.hidden = !visible;

                            option.disabled = !visible;

                        }
                    );

                }


                /*
                |--------------------------------------------------------------------------
                | Organisasi berubah
                |--------------------------------------------------------------------------
                */

                organization.addEventListener(
                    'change',
                    function() {

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
                    function() {

                        filterClasses();

                    }
                );


                /*
                |--------------------------------------------------------------------------
                | Ketika modal dibuka
                |--------------------------------------------------------------------------
                */

                modal.addEventListener(
                    'shown.bs.modal',
                    function() {

                        filterClasses();

                    }
                );


                /*
                |--------------------------------------------------------------------------
                | Reset modal
                |--------------------------------------------------------------------------
                */

                modal.addEventListener(
                    'hidden.bs.modal',
                    function() {

                        form.reset();


                        Array.from(
                            schoolClass.options
                        ).forEach(
                            function(option) {

                                option.hidden =
                                    false;

                                option.disabled =
                                    false;

                            }
                        );


                        schoolClass.value =
                            '';

                    }
                );


                /*
                |--------------------------------------------------------------------------
                | Submit
                |--------------------------------------------------------------------------
                */

                form.addEventListener(
                    'submit',
                    async function(event) {

                        event.preventDefault();

                        submitButton.disabled = true;

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
                                    form.action, {
                                        method: 'POST',

                                        headers: {
                                            'X-CSRF-TOKEN': document
                                                .querySelector(
                                                    'meta[name="csrf-token"]'
                                                )
                                                .getAttribute(
                                                    'content'
                                                ),

                                            'Accept': 'application/json',

                                            'X-Requested-With': 'XMLHttpRequest',
                                        },

                                        body: new FormData(form),
                                    }
                                );


                            /*
                            |--------------------------------------------------------------------------
                            | Berhasil
                            |--------------------------------------------------------------------------
                            */

                            if (response.ok) {

                                window.location.reload();

                                return;
                            }


                            /*
                            |--------------------------------------------------------------------------
                            | Validation error
                            |--------------------------------------------------------------------------
                            */

                            if (response.status === 422) {

                                const data =
                                    await response.json();

                                console.log(
                                    data
                                );

                                return;
                            }


                            throw new Error(
                                'Terjadi kesalahan saat menyimpan data siswa.'
                            );

                        } catch (error) {

                            console.error(
                                error
                            );

                        } finally {

                            submitButton.disabled =
                                false;

                            submitButton.innerHTML = `
                <i class="bi bi-check-lg me-1"></i>
                Simpan Siswa
            `;

                        }

                    }
                );

            }

        );
    </script>
@endpush
