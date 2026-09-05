<div class="modal fade" id="modalCreateTeachingAssignment" tabindex="-1" aria-hidden="true">

    <div class="modal-dialog">

        <div class="modal-content">

            <form method="POST" action="{{ route('admin.teaching-assignments.store') }}">

                @csrf
                <input type="hidden" name="redirect_search" value="{{ request('search') }}">
                <input type="hidden" name="redirect_status" value="{{ request('status') }}">
                <input type="hidden" name="redirect_school_class_id" value="{{ request('school_class_id') }}">
                <input type="hidden" name="redirect_subject_id" value="{{ request('subject_id') }}">

                <div class="modal-header">

                    <h5 class="modal-title">

                        <i class="bi bi-person-plus me-1"></i>

                        Penugasan Mengajar Baru

                    </h5>

                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>

                </div>


                <div class="modal-body">

                    @if ($errors->has('teaching_assignment'))
                        <div class="alert alert-danger">

                            {{ $errors->first('teaching_assignment') }}

                        </div>
                    @endif


                    {{-- Tahun Ajaran --}}

                    <div class="mb-3">

                        <label for="createAcademicYear" class="form-label">
                            Tahun Ajaran
                        </label>

                        <select id="createAcademicYear" class="form-select" required>

                            <option value="">
                                -- Pilih Tahun Ajaran --
                            </option>

                            @foreach ($academicYears as $academicYear)
                                <option value="{{ $academicYear->id }}">
                                    {{ $academicYear->name }}
                                </option>
                            @endforeach

                        </select>

                    </div>


                    {{-- Guru --}}

                    <div class="mb-3">

                        <label for="createTeacher" class="form-label">
                            Guru
                        </label>

                        <select name="teacher_id" id="createTeacher" class="form-select" required>

                            <option value="">
                                -- Pilih Guru --
                            </option>

                            @foreach ($teachers as $teacher)
                                <option value="{{ $teacher->id }}" @selected((string) old('teacher_id') === (string) $teacher->id)>

                                    {{ $teacher->name }}

                                    @if ($teacher->nik)
                                        — {{ $teacher->nik }}
                                    @endif

                                </option>
                            @endforeach

                        </select>

                        @error('teacher_id')
                            <div class="text-danger small mt-1">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    {{-- Mata Pelajaran --}}

                    <div class="mb-3">

                        <label for="createSubject" class="form-label">
                            Mata Pelajaran
                        </label>

                        <select name="subject_id" id="createSubject" class="form-select" required>

                            <option value="">
                                -- Pilih Mata Pelajaran --
                            </option>

                            @foreach ($subjects as $subject)
                                <option value="{{ $subject->id }}" @selected((string) old('subject_id') === (string) $subject->id)>

                                    {{ $subject->name }}

                                    @if (!empty($subject->code))
                                        — {{ $subject->code }}
                                    @endif

                                </option>
                            @endforeach

                        </select>

                        @error('subject_id')
                            <div class="text-danger small mt-1">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    {{-- Kelas --}}

                    <div class="mb-3">

                        <label class="form-label">
                            Kelas yang Diajar
                        </label>

                        <div id="createSchoolClasses" class="border rounded p-3">

                            <div id="createSchoolClassPlaceholder" class="text-muted small">
                                Pilih Tahun Ajaran terlebih dahulu.
                            </div>


                            <div id="createSchoolClassList" class="d-none">

                                {{-- Pilih semua --}}

                                <div class="border-bottom pb-2 mb-2">

                                    <div class="form-check">

                                        <input type="checkbox" class="form-check-input" id="createSelectAllClasses">

                                        <label class="form-check-label fw-semibold" for="createSelectAllClasses">
                                            Pilih Semua Kelas
                                        </label>

                                    </div>

                                </div>


                                {{-- Daftar kelas --}}

                                <div id="createSchoolClassOptions">

                                    @foreach ($schoolClasses as $class)
                                        <div class="form-check create-school-class-item"
                                            data-academic-year="{{ $class->academic_year_id }}">

                                            <input type="checkbox" name="school_class_ids[]"
                                                value="{{ $class->id }}"
                                                class="form-check-input create-school-class-checkbox"
                                                id="createSchoolClass{{ $class->id }}"
                                                data-academic-year="{{ $class->academic_year_id }}"
                                                @checked(in_array($class->id, old('school_class_ids', [])))>

                                            <label class="form-check-label" for="createSchoolClass{{ $class->id }}">
                                                {{ $class->name }}
                                            </label>

                                        </div>
                                    @endforeach

                                </div>

                            </div>

                        </div>


                        @error('school_class_ids')
                            <div class="text-danger small mt-1">
                                {{ $message }}
                            </div>
                        @enderror


                        @error('school_class_ids.*')
                            <div class="text-danger small mt-1">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    {{-- Status --}}

                    <div class="form-check">

                        <input type="hidden" name="is_active" value="0">

                        <input type="checkbox" name="is_active" value="1" class="form-check-input"
                            id="createActive" @checked(old('is_active', true))>

                        <label class="form-check-label" for="createActive">
                            Penugasan aktif
                        </label>

                    </div>

                </div>


                <div class="modal-footer">

                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        Batal
                    </button>

                    <button type="submit" class="btn btn-primary" id="btnSaveTeachingAssignment">

                        <i class="bi bi-save me-1"></i>

                        Simpan

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>
