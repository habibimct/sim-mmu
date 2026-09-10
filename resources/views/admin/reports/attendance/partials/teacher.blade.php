{{-- ==========================================================
FILTER
========================================================== --}}

<form method="GET" class="mb-4">

    <input type="hidden" name="tab" value="guru">

    <div class="row g-3">

        {{-- Tahun Ajaran --}}
        <div class="col-md-3">

            <label class="form-label">
                Tahun Ajaran
            </label>

            <select name="academic_year_id" id="attendanceReportAcademicYear" class="form-select">

                @foreach ($academicYears as $academicYear)
                    <option value="{{ $academicYear->id }}" @selected((string) $academicYearId === (string) $academicYear->id)>
                        {{ $academicYear->name }}
                    </option>
                @endforeach

            </select>

        </div>


        {{-- Unit --}}
        <div class="col-md-3">

            <label class="form-label">
                Unit
            </label>

            <select name="organization_id" id="attendanceReportOrganization" class="form-select">

                <option value="all">
                    Semua Unit
                </option>

                @foreach ($organizations as $organization)
                    <option value="{{ $organization->id }}" @selected((string) $organizationId === (string) $organization->id)>
                        {{ $organization->name }}
                    </option>
                @endforeach

            </select>

        </div>


        {{-- Kelas --}}
        <div class="col-md-3">

            <label class="form-label">
                Kelas
            </label>

            <select name="school_class_id" id="attendanceReportSchoolClass" class="form-select">

                <option value="all">
                    Semua Kelas
                </option>

                @foreach ($schoolClasses as $schoolClass)
                    <option value="{{ $schoolClass->id }}" @selected((string) $schoolClassId === (string) $schoolClass->id)>
                        {{ $schoolClass->name }}
                    </option>
                @endforeach

            </select>

        </div>


        {{-- Mata Pelajaran --}}
        <div class="col-md-3">

            <label class="form-label">
                Mata Pelajaran
            </label>

            <select name="subject_id" class="form-select">

                <option value="all">
                    Semua Mata Pelajaran
                </option>

                @foreach ($subjects as $subject)
                    <option value="{{ $subject->id }}" @selected((string) $subjectId === (string) $subject->id)>
                        {{ $subject->name }}
                    </option>
                @endforeach

            </select>

        </div>

    </div>


    <div class="mt-3 d-flex gap-2">

        <button type="submit" class="btn btn-primary">
            <i class="bi bi-funnel me-1"></i>
            Tampilkan
        </button>

        <a href="{{ route('admin.reports.attendance.index', [
            'tab' => 'guru',
        ]) }}"
            class="btn btn-outline-secondary">
            Reset
        </a>

        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal"
            data-bs-target="#attendanceTimeSettingModal">
            <i class="bi bi-gear me-1"></i>
            Pengaturan Jam
        </button>

        <a href="{{ route('admin.reports.attendance.excel', request()->query()) }}" class="btn btn-success">
            <i class="bi bi-file-earmark-excel me-1"></i>
            Excel
        </a>

        <a href="{{ route('admin.reports.attendance.pdf', request()->query()) }}" class="btn btn-danger"
            target="_blank">
            <i class="bi bi-file-earmark-pdf me-1"></i>
            PDF
        </a>

    </div>

</form>

<div class="modal fade" id="attendanceTimeSettingModal" tabindex="-1" aria-labelledby="attendanceTimeSettingModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title" id="attendanceTimeSettingModalLabel">
                    <i class="bi bi-clock me-1"></i>
                    Pengaturan Jam Absensi Guru
                </h5>

                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>

            </div>


            <div class="modal-body">

                <div class="mb-3">

                    <label class="form-label">
                        Jam Mulai
                    </label>

                    <input type="time" id="teacherAttendanceStartInput" class="form-control"
                        value="{{ request('attendance_start', '07:00') }}">

                </div>


                <div class="mb-3">

                    <label class="form-label">
                        Jam Selesai
                    </label>

                    <input type="time" id="teacherAttendanceEndInput" class="form-control"
                        value="{{ request('attendance_end', '16:00') }}">

                </div>


                <div class="mb-3">

                    <label class="form-label">
                        Interval
                    </label>

                    <select id="teacherAttendanceIntervalInput" class="form-select">

                        @foreach ([15, 30, 45, 60] as $interval)
                            <option value="{{ $interval }}" @selected((int) request('attendance_interval', 30) === $interval)>
                                {{ $interval }} menit
                            </option>
                        @endforeach

                    </select>

                </div>

            </div>


            <div class="modal-footer">

                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Tutup
                </button>

                <button type="button" class="btn btn-primary" id="applyTeacherAttendanceTime">
                    <i class="bi bi-check-lg me-1"></i>
                    Terapkan
                </button>

            </div>

        </div>

    </div>
</div>

{{-- ==========================================================
KARTU MINGGUAN
========================================================== --}}

<div class="card">

    <div class="card-header">

        <div class="d-flex justify-content-between align-items-center">

            <div>

                <div class="d-flex align-items-center gap-2">

                    <i class="bi bi-calendar3"></i>

                    <strong>
                        Jadwal Mingguan
                    </strong>

                    <small class="text-muted">

                        {{ $weekStart->translatedFormat('d M Y') }}

                        –

                        {{ $weekEnd->translatedFormat('d M Y') }}

                    </small>

                </div>

            </div>


            <div class="btn-group">

                {{-- Minggu Sebelumnya --}}
                <a href="{{ request()->fullUrlWithQuery([
                    'tab' => 'guru',
                    'week' => $weekStart->copy()->subWeek()->format('Y-m-d'),
                ]) }}"
                    class="btn btn-sm btn-outline-secondary" title="Minggu sebelumnya">
                    <i class="fas fa-chevron-left"></i>
                </a>


                {{-- Minggu Ini --}}
                <a href="{{ request()->fullUrlWithQuery([
                    'tab' => 'guru',
                    'week' => now()->format('Y-m-d'),
                ]) }}"
                    class="btn btn-sm btn-outline-primary">
                    Minggu Ini
                </a>


                {{-- Minggu Berikutnya --}}
                <a href="{{ request()->fullUrlWithQuery([
                    'tab' => 'guru',
                    'week' => $weekStart->copy()->addWeek()->format('Y-m-d'),
                ]) }}"
                    class="btn btn-sm btn-outline-secondary" title="Minggu berikutnya">
                    <i class="fas fa-chevron-right"></i>
                </a>

            </div>

        </div>

    </div>


    {{-- ======================================================
    KALENDER
    ======================================================= --}}

    <div class="card-body p-0">

        <div class="table-responsive">

            <table class="table table-bordered table-hover mb-0" style="min-width: 1100px;">

                <thead class="table-light">

                    <tr>

                        <th style="width: 75px;" class="text-center align-middle">
                            Jam
                        </th>

                        @foreach ([
        1 => 'Senin',
        2 => 'Selasa',
        3 => 'Rabu',
        4 => 'Kamis',
        5 => 'Jumat',
        6 => 'Sabtu',
        7 => 'Minggu',
    ] as $dayNumber => $dayName)
                            <th class="text-center">

                                {{ $dayName }}

                            </th>
                        @endforeach

                    </tr>

                </thead>


                <tbody>

                    @foreach ($teacherAttendanceHours as $hour)
                        <tr>

                            <td class="text-center align-top fw-bold text-muted" style="height: 70px;">
                                {{ $hour }}
                            </td>


                            @foreach (range(1, 7) as $dayNumber)
                                @php

                                    $cellStart = \Carbon\Carbon::createFromFormat('H:i', $hour);

                                    $cellEnd = $cellStart->copy()->addMinutes($teacherAttendanceInterval);

                                    $cellAttendances = $weeklyAttendances->filter(function ($attendance) use (
                                        $cellStart,
                                        $cellEnd,
                                        $dayNumber,
                                    ) {
                                        $attendanceTime = \Carbon\Carbon::parse($attendance->created_at);

                                        return $attendance->date->dayOfWeekIso === $dayNumber &&
                                            $attendanceTime->format('H:i') >= $cellStart->format('H:i') &&
                                            $attendanceTime->format('H:i') < $cellEnd->format('H:i');
                                    });

                                @endphp


                                <td class="align-top"
                                    style="
                                        min-width: 150px;
                                        height: 70px;
                                    ">

                                    @forelse ($cellAttendances
                                        as $attendance)
                                        <div class="p-2 mb-1 rounded bg-light border">

                                            <div class="fw-bold text-primary">
                                                {{ $attendance->teachingAssignment->schoolClass->name }}
                                            </div>


                                            <div class="small">

                                                {{ $attendance->teachingAssignment->subject->name }}

                                            </div>


                                            <div class="small text-muted">

                                                {{ $attendance->teachingAssignment->teacher->name }}

                                            </div>


                                            <div class="small text-muted">

                                                Input
                                                {{ $attendance->created_at->format('H:i') }}

                                            </div>

                                        </div>

                                    @empty

                                        {{-- Kosong --}}
                                    @endforelse

                                </td>
                            @endforeach

                        </tr>
                    @endforeach

                </tbody>

            </table>

        </div>

    </div>

</div>


@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const academicYearSelect =
                document.getElementById('attendanceReportAcademicYear');

            const organizationSelect =
                document.getElementById('attendanceReportOrganization');

            const schoolClassSelect =
                document.getElementById('attendanceReportSchoolClass');

            if (
                !academicYearSelect ||
                !organizationSelect ||
                !schoolClassSelect
            ) {
                return;
            }

            const selectedClassId = @json($schoolClassId);

            async function loadClasses() {

                const academicYearId = academicYearSelect.value;
                const organizationId = organizationSelect.value;

                schoolClassSelect.disabled = true;

                schoolClassSelect.innerHTML =
                    '<option value="all">Memuat kelas...</option>';

                const params = new URLSearchParams();

                if (academicYearId) {
                    params.append(
                        'academic_year_id',
                        academicYearId
                    );
                }

                if (
                    organizationId &&
                    organizationId !== 'all'
                ) {
                    params.append(
                        'organization_id',
                        organizationId
                    );
                }

                try {

                    const response = await fetch(
                        `{{ route('admin.reports.attendance.classes') }}?${params.toString()}`, {
                            method: 'GET',

                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                            }
                        }
                    );

                    if (!response.ok) {
                        throw new Error(
                            'Gagal mengambil data kelas.'
                        );
                    }

                    const classes = await response.json();

                    schoolClassSelect.innerHTML =
                        '<option value="all">Semua Kelas</option>';

                    classes.forEach(function(schoolClass) {

                        const option =
                            document.createElement('option');

                        option.value = schoolClass.id;
                        option.textContent = schoolClass.name;

                        if (
                            selectedClassId &&
                            String(selectedClassId) ===
                            String(schoolClass.id)
                        ) {
                            option.selected = true;
                        }

                        schoolClassSelect.appendChild(option);

                    });

                } catch (error) {

                    console.error(error);

                    schoolClassSelect.innerHTML =
                        '<option value="all">Gagal memuat kelas</option>';

                } finally {

                    schoolClassSelect.disabled = false;

                }
            }


            academicYearSelect.addEventListener(
                'change',
                loadClasses
            );

            organizationSelect.addEventListener(
                'change',
                loadClasses
            );

        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const applyButton =
                document.getElementById(
                    'applyTeacherAttendanceTime'
                );

            if (!applyButton) {
                return;
            }

            applyButton.addEventListener('click', function() {

                const start =
                    document.getElementById(
                        'teacherAttendanceStartInput'
                    ).value;

                const end =
                    document.getElementById(
                        'teacherAttendanceEndInput'
                    ).value;

                const interval =
                    document.getElementById(
                        'teacherAttendanceIntervalInput'
                    ).value;

                if (!start || !end) {
                    alert(
                        'Jam mulai dan jam selesai harus diisi.'
                    );

                    return;
                }

                if (start >= end) {
                    alert(
                        'Jam selesai harus lebih besar dari jam mulai.'
                    );

                    return;
                }

                const url =
                    new URL(
                        window.location.href
                    );

                url.searchParams.set(
                    'attendance_start',
                    start
                );

                url.searchParams.set(
                    'attendance_end',
                    end
                );

                url.searchParams.set(
                    'attendance_interval',
                    interval
                );

                window.location.href =
                    url.toString();

            });

        });
    </script>
@endpush
