<div class="modal fade"
    id="studentAttendanceFilterModal"
    tabindex="-1"
    aria-labelledby="studentAttendanceFilterModalLabel"
    aria-hidden="true">

    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content">

            <form method="GET"
                action="{{ route('admin.student-attendance.index') }}">

                {{-- Header --}}
                <div class="modal-header">

                    <h5 class="modal-title"
                        id="studentAttendanceFilterModalLabel">

                        <i class="bi bi-funnel me-1"></i>
                        Filter Student Attendance

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

                            <label for="studentAttendanceAcademicYear"
                                class="form-label">

                                Tahun Akademik

                            </label>

                            <select
                                id="studentAttendanceAcademicYear"
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

                            <label for="studentAttendanceOrganization"
                                class="form-label">

                                Unit

                            </label>

                            <select
                                id="studentAttendanceOrganization"
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

                            <label for="studentAttendanceSchoolClass"
                                class="form-label">

                                Kelas

                            </label>

                            <select
                                id="studentAttendanceSchoolClass"
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

                        {{-- Siswa --}}
                        <div class="col-md-6 mb-3">

                            <label for="studentAttendanceStudent"
                                class="form-label">

                                Siswa

                            </label>

                            <select
                                id="studentAttendanceStudent"
                                name="student_id"
                                class="form-select">

                                <option value="all">
                                    Semua Siswa
                                </option>

                                @foreach ($studentOptions as $id => $name)

                                    <option
                                        value="{{ $id }}"
                                        @selected(
                                            (string) $studentId ===
                                            (string) $id
                                        )>

                                        {{ $name }}

                                    </option>

                                @endforeach

                            </select>

                        </div>

                        {{-- Mata Pelajaran --}}
                        <div class="col-md-6 mb-3">

                            <label for="studentAttendanceSubject"
                                class="form-label">

                                Mata Pelajaran

                            </label>

                            <select
                                id="studentAttendanceSubject"
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

                    <a href="{{ route('admin.student-attendance.index') }}"
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

                        <i class="bi bi-check-lg me-1"></i>
                        Terapkan

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>
