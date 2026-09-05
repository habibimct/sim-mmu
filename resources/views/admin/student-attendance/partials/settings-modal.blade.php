<div class="modal fade"
    id="studentAttendanceSettingsModal"
    tabindex="-1"
    aria-labelledby="studentAttendanceSettingsModalLabel"
    aria-hidden="true">

    <div class="modal-dialog modal-md modal-dialog-centered">

        <div class="modal-content">

            <form method="GET"
                action="{{ route('admin.student-attendance.index') }}">

                {{-- Pertahankan filter yang sedang aktif --}}
                @if ($academicYearId !== 'all')
                    <input type="hidden"
                        name="academic_year_id"
                        value="{{ $academicYearId }}">
                @endif

                @if ($organizationId !== 'all')
                    <input type="hidden"
                        name="organization_id"
                        value="{{ $organizationId }}">
                @endif

                @if ($schoolClassId !== 'all')
                    <input type="hidden"
                        name="school_class_id"
                        value="{{ $schoolClassId }}">
                @endif

                @if ($studentId !== 'all')
                    <input type="hidden"
                        name="student_id"
                        value="{{ $studentId }}">
                @endif

                @if ($subjectId !== 'all')
                    <input type="hidden"
                        name="subject_id"
                        value="{{ $subjectId }}">
                @endif

                {{-- Header --}}
                <div class="modal-header">

                    <h5 class="modal-title"
                        id="studentAttendanceSettingsModalLabel">

                        <i class="bi bi-gear me-1"></i>
                        Pengaturan Chart

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

                        {{-- Jam Mulai --}}
                        <div class="col-md-6 mb-3">

                            <label for="studentAttendanceStartHour"
                                class="form-label">

                                Jam Mulai

                            </label>

                            <select
                                id="studentAttendanceStartHour"
                                name="start_hour"
                                class="form-select">

                                @for ($hour = 5; $hour <= 12; $hour++)

                                    <option
                                        value="{{ $hour }}"
                                        @selected(
                                            (int) request('start_hour', 7) === $hour
                                        )>
                                        {{ sprintf('%02d:00', $hour) }}
                                    </option>

                                @endfor

                            </select>

                        </div>

                        {{-- Jam Selesai --}}
                        <div class="col-md-6 mb-3">

                            <label for="studentAttendanceEndHour"
                                class="form-label">

                                Jam Selesai

                            </label>

                            <select
                                id="studentAttendanceEndHour"
                                name="end_hour"
                                class="form-select">

                                @for ($hour = 13; $hour <= 21; $hour++)

                                    <option
                                        value="{{ $hour }}"
                                        @selected(
                                            (int) request('end_hour', 16) === $hour
                                        )>
                                        {{ sprintf('%02d:00', $hour) }}
                                    </option>

                                @endfor

                            </select>

                        </div>

                        {{-- Interval --}}
                        <div class="col-12 mb-3">

                            <label for="studentAttendanceInterval"
                                class="form-label">

                                Interval

                            </label>

                            <select
                                id="studentAttendanceInterval"
                                name="interval"
                                class="form-select">

                                <option
                                    value="15"
                                    @selected(request('interval', 60) == 15)>
                                    15 menit
                                </option>

                                <option
                                    value="30"
                                    @selected(request('interval', 60) == 30)>
                                    30 menit
                                </option>

                                <option
                                    value="45"
                                    @selected(request('interval', 60) == 45)>
                                    45 menit
                                </option>

                                <option
                                    value="60"
                                    @selected(request('interval', 60) == 60)>
                                    60 menit
                                </option>

                            </select>

                        </div>

                    </div>

                </div>

                {{-- Footer --}}
                <div class="modal-footer">

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
