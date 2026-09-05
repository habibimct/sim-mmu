<div class="modal fade"
    id="teacherAttendanceSettingsModal"
    tabindex="-1"
    aria-labelledby="teacherAttendanceSettingsModalLabel"
    aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <form method="GET"
                action="{{ route('admin.teacher-attendance.index') }}">

                {{-- Pertahankan filter --}}
                <input type="hidden"
                    name="academic_year_id"
                    value="{{ $academicYearId }}">

                <input type="hidden"
                    name="organization_id"
                    value="{{ $organizationId ?? 'all' }}">

                <input type="hidden"
                    name="school_class_id"
                    value="{{ $schoolClassId ?? 'all' }}">

                <input type="hidden"
                    name="subject_id"
                    value="{{ $subjectId ?? 'all' }}">

                <input type="hidden"
                    name="week"
                    value="{{ $week }}">


                {{-- Header --}}
                <div class="modal-header">

                    <h5 class="modal-title"
                        id="teacherAttendanceSettingsModalLabel">

                        <i class="fas fa-cog me-1"></i>
                        Pengaturan Tampilan Jadwal

                    </h5>

                    <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close">
                    </button>

                </div>


                {{-- Body --}}
                <div class="modal-body">

                    {{-- Jam Mulai --}}
                    <div class="mb-3">

                        <label for="teacherAttendanceStart">
                            Jam Mulai
                        </label>

                        <input
                            type="time"
                            id="teacherAttendanceStart"
                            name="teacherAttendance_start"
                            value="{{ $teacherAttendanceStart }}"
                            class="form-control">

                    </div>


                    {{-- Jam Selesai --}}
                    <div class="mb-3">

                        <label for="teacherAttendanceEnd">
                            Jam Selesai
                        </label>

                        <input
                            type="time"
                            id="teacherAttendanceEnd"
                            name="teacherAttendance_end"
                            value="{{ $teacherAttendanceEnd }}"
                            class="form-control">

                    </div>


                    {{-- Interval --}}
                    <div class="mb-0">

                        <label for="teacherAttendanceInterval">
                            Interval
                        </label>

                        <select
                            id="teacherAttendanceInterval"
                            name="teacherAttendance_interval"
                            class="form-select">

                            @foreach ([15, 30, 45, 60] as $interval)

                                <option
                                    value="{{ $interval }}"
                                    @selected($teacherAttendanceInterval == $interval)>

                                    {{ $interval }} menit

                                </option>

                            @endforeach

                        </select>

                    </div>

                </div>


                {{-- Footer --}}
                <div class="modal-footer">

                    <button type="button"
                        class="btn btn-secondary"
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
