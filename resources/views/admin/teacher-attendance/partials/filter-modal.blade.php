<div class="modal fade"
    id="teacherAttendanceFilterModal"
    tabindex="-1"
    aria-labelledby="teacherAttendanceFilterModalLabel"
    aria-hidden="true">

    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content">

            <form method="GET"
                action="{{ route('admin.teacher-attendance.index') }}">

                {{-- Header --}}
                <div class="modal-header">

                    <h5 class="modal-title"
                        id="teacherAttendanceFilterModalLabel">

                        <i class="fas fa-filter me-1"></i>
                        Filter Jadwal

                    </h5>

                    <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close">
                    </button>

                </div>


                {{-- Body --}}
                <div class="modal-body">

                    <div class="row">

                        {{-- Tahun Akademik --}}
                        <div class="col-md-6 mb-3">

                            <label for="teacherAttendanceAcademicYear">
                                Tahun Akademik
                            </label>

                            <select
                                id="teacherAttendanceAcademicYear"
                                name="academic_year_id"
                                class="form-select">

                                <option value="all">
                                    Semua Tahun Akademik
                                </option>

                                @foreach ($academicYears as $academicYear)

                                    <option
                                        value="{{ $academicYear->id }}"
                                        @selected(
                                            (string) $academicYearId ===
                                            (string) $academicYear->id
                                        )>

                                        {{ $academicYear->name }}

                                    </option>

                                @endforeach

                            </select>

                        </div>


                        {{-- Unit --}}
                        <div class="col-md-6 mb-3">

                            <label for="teacherAttendanceOrganization">
                                Unit
                            </label>

                            <select
                                id="teacherAttendanceOrganization"
                                name="organization_id"
                                class="form-select">

                                <option value="all">
                                    Semua Unit
                                </option>

                                @foreach ($organizationOptions as $id => $name)

                                    <option
                                        value="{{ $id }}"
                                        @selected(
                                            (string) $organizationId ===
                                            (string) $id
                                        )>

                                        {{ $name }}

                                    </option>

                                @endforeach

                            </select>

                        </div>


                        {{-- Kelas --}}
                        <div class="col-md-6 mb-3">

                            <label for="teacherAttendanceSchoolClass">
                                Kelas
                            </label>

                            <select
                                id="teacherAttendanceSchoolClass"
                                name="school_class_id"
                                class="form-select">

                                <option value="all">
                                    Semua Kelas
                                </option>

                                @foreach ($classOptions as $id => $name)

                                    <option
                                        value="{{ $id }}"
                                        @selected(
                                            (string) $schoolClassId ===
                                            (string) $id
                                        )>

                                        {{ $name }}

                                    </option>

                                @endforeach

                            </select>

                        </div>


                        {{-- Mata Pelajaran --}}
                        <div class="col-md-6 mb-3">

                            <label for="teacherAttendanceSubject">
                                Mata Pelajaran
                            </label>

                            <select
                                id="teacherAttendanceSubject"
                                name="subject_id"
                                class="form-select">

                                <option value="all">
                                    Semua Mata Pelajaran
                                </option>

                                @foreach ($subjectOptions as $id => $name)

                                    <option
                                        value="{{ $id }}"
                                        @selected(
                                            (string) $subjectId ===
                                            (string) $id
                                        )>

                                        {{ $name }}

                                    </option>

                                @endforeach

                            </select>

                        </div>

                    </div>

                </div>


                {{-- Footer --}}
                <div class="modal-footer">

                    <a href="{{ route('admin.teacher-attendance.index') }}"
                        class="btn btn-secondary">

                        Reset

                    </a>

                    <button type="button"
                        class="btn btn-outline-secondary"
                        data-bs-dismiss="modal">

                        Batal

                    </button>

                    <button type="submit"
                        class="btn btn-primary">

                        <i class="fas fa-check me-1"></i>
                        Terapkan

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>



@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const academicYearSelect =
                document.getElementById('teacherAttendanceAcademicYear');

            const organizationSelect =
                document.getElementById('teacherAttendanceOrganization');

            const classSelect =
                document.getElementById('teacherAttendanceSchoolClass');

            const subjectSelect =
                document.getElementById('teacherAttendanceSubject');


            if (!academicYearSelect || !organizationSelect || !classSelect) {
                return;
            }


            /*
             * Memuat ulang pilihan Kelas dan Mata Pelajaran
             * berdasarkan Tahun Akademik dan Unit.
             */
            function loadFilterOptions() {

                const academicYearId = academicYearSelect.value;
                const organizationId = organizationSelect.value;


                const params = new URLSearchParams({
                    academic_year_id: academicYearId,
                    organization_id: organizationId,
                });


                classSelect.disabled = true;

                if (subjectSelect) {
                    subjectSelect.disabled = true;
                }


                classSelect.innerHTML = `
                    <option value="all">
                        Memuat kelas...
                    </option>
                `;


                if (subjectSelect) {
                    subjectSelect.innerHTML = `
                        <option value="all">
                            Memuat mata pelajaran...
                        </option>
                    `;
                }


                fetch(
                    `{{ route('admin.teacher-attendance.filter-options') }}?${params.toString()}`,
                    {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        }
                    }
                )
                .then(response => {

                    if (!response.ok) {
                        throw new Error('Gagal mengambil data filter.');
                    }

                    return response.json();

                })
                .then(data => {

                    /*
                     * ============================
                     * KELAS
                     * ============================
                     */

                    classSelect.innerHTML = `
                        <option value="all">
                            Semua Kelas
                        </option>
                    `;


                    if (data.classes) {

                        Object.entries(data.classes).forEach(
                            ([id, name]) => {

                                const option =
                                    document.createElement('option');

                                option.value = id;
                                option.textContent = name;

                                classSelect.appendChild(option);

                            }
                        );

                    }


                    /*
                     * ============================
                     * MATA PELAJARAN
                     * ============================
                     */

                    if (subjectSelect) {

                        subjectSelect.innerHTML = `
                            <option value="all">
                                Semua Mata Pelajaran
                            </option>
                        `;


                        if (data.subjects) {

                            Object.entries(data.subjects).forEach(
                                ([id, name]) => {

                                    const option =
                                        document.createElement('option');

                                    option.value = id;
                                    option.textContent = name;

                                    subjectSelect.appendChild(option);

                                }
                            );

                        }

                    }


                    classSelect.disabled = false;

                    if (subjectSelect) {
                        subjectSelect.disabled = false;
                    }

                })
                .catch(error => {

                    console.error(error);


                    classSelect.innerHTML = `
                        <option value="all">
                            Gagal memuat kelas
                        </option>
                    `;


                    if (subjectSelect) {

                        subjectSelect.innerHTML = `
                            <option value="all">
                                Gagal memuat mata pelajaran
                            </option>
                        `;

                    }

                    classSelect.disabled = false;

                    if (subjectSelect) {
                        subjectSelect.disabled = false;
                    }

                });

            }


            /*
             * Tahun Akademik berubah
             */
            academicYearSelect.addEventListener(
                'change',
                loadFilterOptions
            );


            /*
             * Unit berubah
             *
             * Karena Kelas dan Mata Pelajaran juga
             * bergantung pada Unit, keduanya ikut diperbarui.
             */
            organizationSelect.addEventListener(
                'change',
                loadFilterOptions
            );

        });
    </script>
@endpush
