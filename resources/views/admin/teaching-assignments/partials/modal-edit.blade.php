<div class="modal fade" id="modalEditTeachingAssignment" tabindex="-1" aria-hidden="true">

    <div class="modal-dialog">

        <div class="modal-content">

            <form method="POST" id="formEditTeachingAssignment">

                @csrf
                @method('PUT')

                <input type="hidden" name="redirect_search" value="{{ request('search') }}">
                <input type="hidden" name="redirect_status" value="{{ request('status') }}">
                <input type="hidden" name="redirect_school_class_id" value="{{ request('school_class_id') }}">
                <input type="hidden" name="redirect_subject_id" value="{{ request('subject_id') }}">

                <div class="modal-header">

                    <h5 class="modal-title">

                        <i class="bi bi-pencil-square me-1"></i>

                        Edit Penugasan Mengajar

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

                        <label for="editAcademicYear" class="form-label">
                            Tahun Ajaran
                        </label>

                        <select id="editAcademicYear" class="form-select" required>

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

                        <label for="editTeacher" class="form-label">
                            Guru
                        </label>

                        <select name="teacher_id" id="editTeacher" class="form-select" required>

                            <option value="">
                                -- Pilih Guru --
                            </option>

                            @foreach ($teachers as $teacher)
                                <option value="{{ $teacher->id }}">

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

                        <label for="editSubject" class="form-label">
                            Mata Pelajaran
                        </label>

                        <select name="subject_id" id="editSubject" class="form-select" required>

                            <option value="">
                                -- Pilih Mata Pelajaran --
                            </option>

                            @foreach ($subjects as $subject)
                                <option value="{{ $subject->id }}">

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

                        <label for="editSchoolClass" class="form-label">
                            Kelas yang Diajar
                        </label>

                        <select name="school_class_ids[]" id="editSchoolClass" class="form-select" required>

                            <option value="">
                                Pilih Kelas
                            </option>

                            @foreach ($schoolClasses as $class)
                                <option value="{{ $class->id }}"
                                    data-academic-year="{{ $class->academic_year_id }}">
                                    {{ $class->name }}
                                </option>
                            @endforeach

                        </select>


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

                        <input type="checkbox" name="is_active" value="1" class="form-check-input" id="editActive">

                        <label class="form-check-label" for="editActive">
                            Penugasan aktif
                        </label>

                    </div>

                </div>


                <div class="modal-footer">

                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        Batal
                    </button>

                    <button type="submit" class="btn btn-primary">

                        <i class="bi bi-save me-1"></i>

                        Simpan Perubahan

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>
