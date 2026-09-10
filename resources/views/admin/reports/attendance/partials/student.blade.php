{{-- ==========================================================
FILTER ABSENSI SISWA
========================================================== --}}

<form method="GET">

    <input type="hidden" name="tab" value="siswa">

    <div class="row g-3">

        {{-- Bulan --}}
        <div class="col-md-2">

            <label class="form-label">
                Bulan
            </label>

            <select name="month" class="form-select">

                @php

                    $months = [
                        1 => 'Januari',
                        2 => 'Februari',
                        3 => 'Maret',
                        4 => 'April',
                        5 => 'Mei',
                        6 => 'Juni',
                        7 => 'Juli',
                        8 => 'Agustus',
                        9 => 'September',
                        10 => 'Oktober',
                        11 => 'November',
                        12 => 'Desember',
                    ];

                @endphp

                @foreach ($months as $number => $name)
                    <option value="{{ $number }}" @selected($month === $number)>
                        {{ $name }}
                    </option>
                @endforeach

            </select>

        </div>


        {{-- Tahun --}}
        <div class="col-md-2">

            <label class="form-label">
                Tahun
            </label>

            <input type="number" name="year" value="{{ $year }}" class="form-control" min="2020"
                max="2100">

        </div>


        {{-- Tahun Ajaran --}}
        <div class="col-md-2">

            <label class="form-label">
                Tahun Ajaran
            </label>

            <select name="academic_year_id" id="attendanceStudentAcademicYear" class="form-select">

                @foreach ($academicYears as $academicYear)
                    <option value="{{ $academicYear->id }}" @selected((string) $academicYearId === (string) $academicYear->id)>
                        {{ $academicYear->name }}
                    </option>
                @endforeach

            </select>

        </div>


        {{-- Unit --}}
        <div class="col-md-2">

            <label class="form-label">
                Unit
            </label>

            <select name="organization_id" id="attendanceStudentOrganization" class="form-select">

                <option value="all">
                    Semua
                </option>

                @foreach ($organizations as $organization)
                    <option value="{{ $organization->id }}" @selected((string) $organizationId === (string) $organization->id)>
                        {{ $organization->name }}
                    </option>
                @endforeach

            </select>

        </div>


        {{-- Kelas --}}
        <div class="col-md-2">

            <label class="form-label">
                Kelas
            </label>

            <select name="school_class_id" id="attendanceStudentSchoolClass" class="form-select">

                <option value="all">
                    Semua
                </option>

                @foreach ($schoolClasses as $schoolClass)
                    <option value="{{ $schoolClass->id }}" @selected((string) $schoolClassId === (string) $schoolClass->id)>
                        {{ $schoolClass->name }}
                    </option>
                @endforeach

            </select>

        </div>

        {{-- Kelas --}}
        <div class="col-md-2">
            <label class="form-label" for="attendanceReportSubject">
                Mata Pelajaran
            </label>

            <select name="subject_id" id="attendanceReportSubject" class="form-select">
                <option value="all">
                    Semua Mata Pelajaran
                </option>

                @foreach ($subjects as $subject)
                    <option value="{{ $subject->id }}" @selected($subjectId == $subject->id)>
                        {{ $subject->name }}
                    </option>
                @endforeach
            </select>
        </div>

    </div>


    <div class="mt-3">

        <button type="submit" class="btn btn-primary">
            <i class="bi bi-funnel me-1"></i>
            Tampilkan
        </button>

        <a href="{{ route('admin.reports.attendance.index', [
            'tab' => 'siswa',
        ]) }}"
            class="btn btn-outline-secondary">
            Reset
        </a>

        <a href="{{ route('admin.reports.attendance.student.excel', request()->query()) }}" class="btn btn-success">
            <i class="bi bi-file-earmark-excel me-1"></i>
            Excel
        </a>

        <a href="{{ route('admin.reports.attendance.student.pdf', request()->query()) }}" class="btn btn-danger"
            target="_blank">
            <i class="fas fa-file-pdf"></i>
            PDF
        </a>

    </div>

</form>


<hr class="my-4">


{{-- ==========================================================
HEADER BULAN
========================================================== --}}

<div class="d-flex justify-content-between align-items-center mb-3">

    <div>

        <h5 class="mb-1">

            <i class="bi bi-calendar3 me-1"></i>

            Absensi Siswa

        </h5>

        <div class="text-muted">

            {{ $monthStart->translatedFormat('F Y') }}

        </div>

    </div>


    <span class="badge bg-primary">

        {{ $studentAcademicYears->count() }}
        siswa

    </span>

</div>


{{-- ==========================================================
TABEL
========================================================== --}}

<div class="table-responsive">

    <table class="table table-bordered table-sm">

        <thead class="table-light">

            <tr>

                <th style="min-width: 220px;" class="align-middle">
                    Siswa
                </th>

                @for ($day = $monthStart->copy(); $day <= $monthEnd; $day->addDay())
                    <th class="text-center" style="min-width: 35px;">
                        {{ $day->day }}
                    </th>
                @endfor

                <th class="text-center">
                    H
                </th>

                <th class="text-center">
                    S
                </th>

                <th class="text-center">
                    I
                </th>

                <th class="text-center">
                    A
                </th>

            </tr>

        </thead>


        <tbody>

            @forelse (
                $studentAcademicYears
                as $studentAcademicYear
            )

                @php

                    $details = $attendanceDetails[$studentAcademicYear->id] ?? collect();

                    /*
                    |--------------------------------------------------------------------------
                    | Rekap per hari
                    |--------------------------------------------------------------------------
                    */
                    $dailyStatuses = $details
                        ->filter(fn($detail) => $detail->attendance?->date)
                        ->groupBy(fn($detail) => $detail->attendance->date->format('Y-m-d'))
                        ->map(function ($dayDetails) {
                            $statuses = $dayDetails->pluck('status');

                            // Jika pernah hadir, hari dianggap HADIR.
                            if ($statuses->contains('present')) {
                                return 'present';
                            }

                            // Tidak ada hadir → cari status terbanyak.
                            $counts = [
                                'sick' => $statuses->filter(fn($status) => $status === 'sick')->count(),

                                'permission' => $statuses->filter(fn($status) => $status === 'permission')->count(),

                                'absent' => $statuses->filter(fn($status) => $status === 'absent')->count(),
                            ];

                            // Prioritas ketika jumlah sama:
                            // Sakit > Izin > Alpa
                            arsort($counts);

                            return array_key_first($counts);
                        });

                    $present = $dailyStatuses->where(fn($status) => $status === 'present')->count();

                    $sick = $dailyStatuses->where(fn($status) => $status === 'sick')->count();

                    $permission = $dailyStatuses->where(fn($status) => $status === 'permission')->count();

                    $absent = $dailyStatuses->where(fn($status) => $status === 'absent')->count();

                @endphp


                <tr>

                    <td>

                        <div class="fw-semibold">

                            {{ $studentAcademicYear->student->name }}

                        </div>

                        <div class="small text-muted">

                            {{ $studentAcademicYear->schoolClass->name }}

                        </div>

                    </td>


                    @for ($day = $monthStart->copy(); $day <= $monthEnd; $day->addDay())
                        @php

                            $dailyStatus = $dailyStatuses[$day->format('Y-m-d')] ?? null;

                        @endphp


                        <td class="text-center align-middle">

                            @if ($dailyStatus)
                                @switch($dailyStatus)
                                    @case('present')
                                        <span class="text-success fw-bold">
                                            H
                                        </span>
                                    @break

                                    @case('sick')
                                        <span class="text-warning fw-bold">
                                            S
                                        </span>
                                    @break

                                    @case('permission')
                                        <span class="text-info fw-bold">
                                            I
                                        </span>
                                    @break

                                    @case('absent')
                                        <span class="text-danger fw-bold">
                                            A
                                        </span>
                                    @break
                                @endswitch
                            @else
                                <span class="text-muted">
                                    -
                                </span>
                            @endif

                        </td>
                    @endfor


                    <td class="text-center text-success fw-bold">
                        {{ $present }}
                    </td>

                    <td class="text-center text-warning fw-bold">
                        {{ $sick }}
                    </td>

                    <td class="text-center text-info fw-bold">
                        {{ $permission }}
                    </td>

                    <td class="text-center text-danger fw-bold">
                        {{ $absent }}
                    </td>

                </tr>

                @empty

                    <tr>

                        <td colspan="40" class="text-center text-muted py-4">
                            Belum ada data siswa.
                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>


    <div class="small text-muted mt-2">

        <strong>H</strong> = Hadir &nbsp;·&nbsp;
        <strong>S</strong> = Sakit &nbsp;·&nbsp;
        <strong>I</strong> = Izin &nbsp;·&nbsp;
        <strong>A</strong> = Alpa

    </div>



    @push('js')
        <script>
            document.addEventListener('DOMContentLoaded', function() {

                const academicYearSelect =
                    document.getElementById('attendanceStudentAcademicYear');

                const organizationSelect =
                    document.getElementById('attendanceStudentOrganization');

                const schoolClassSelect =
                    document.getElementById('attendanceStudentSchoolClass');

                const subjectSelect =
                    document.getElementById('attendanceReportSubject');


                if (
                    !academicYearSelect ||
                    !organizationSelect ||
                    !schoolClassSelect ||
                    !subjectSelect
                ) {
                    return;
                }


                const selectedClassId =
                    @json($schoolClassId);

                const selectedSubjectId =
                    @json($subjectId);


                // ==========================================================
                // LOAD KELAS
                // ==========================================================

                async function loadClasses() {

                    const academicYearId =
                        academicYearSelect.value;

                    const organizationId =
                        organizationSelect.value;


                    schoolClassSelect.disabled = true;

                    schoolClassSelect.innerHTML =
                        '<option value="all">Memuat kelas...</option>';


                    const params =
                        new URLSearchParams();


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


                        const classes =
                            await response.json();


                        schoolClassSelect.innerHTML =
                            '<option value="all">Semua</option>';


                        classes.forEach(function(schoolClass) {

                            const option =
                                document.createElement('option');


                            option.value =
                                schoolClass.id;


                            option.textContent =
                                schoolClass.name;


                            if (
                                selectedClassId &&
                                String(selectedClassId) ===
                                String(schoolClass.id)
                            ) {

                                option.selected = true;

                            }


                            schoolClassSelect.appendChild(
                                option
                            );

                        });


                    } catch (error) {

                        console.error(error);


                        schoolClassSelect.innerHTML =
                            '<option value="all">Gagal memuat kelas</option>';


                    } finally {

                        schoolClassSelect.disabled = false;

                    }


                    // Setelah kelas selesai dimuat,
                    // mapel ikut diperbarui.
                    await loadSubjects();

                }


                // ==========================================================
                // LOAD MAPEL
                // ==========================================================

                async function loadSubjects() {

                    const academicYearId =
                        academicYearSelect.value;

                    const organizationId =
                        organizationSelect.value;

                    const schoolClassId =
                        schoolClassSelect.value;


                    subjectSelect.disabled = true;

                    subjectSelect.innerHTML =
                        '<option value="all">Memuat mata pelajaran...</option>';


                    const params =
                        new URLSearchParams();


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


                    if (
                        schoolClassId &&
                        schoolClassId !== 'all'
                    ) {

                        params.append(
                            'school_class_id',
                            schoolClassId
                        );

                    }


                    try {

                        const response = await fetch(
                            `{{ route('admin.reports.attendance.subjects') }}?${params.toString()}`, {
                                method: 'GET',

                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json',
                                }
                            }
                        );


                        if (!response.ok) {

                            throw new Error(
                                'Gagal mengambil data mata pelajaran.'
                            );

                        }


                        const subjects =
                            await response.json();


                        subjectSelect.innerHTML =
                            '<option value="all">Semua Mata Pelajaran</option>';


                        subjects.forEach(function(subject) {

                            const option =
                                document.createElement('option');


                            option.value =
                                subject.id;


                            option.textContent =
                                subject.name;


                            if (
                                selectedSubjectId &&
                                String(selectedSubjectId) ===
                                String(subject.id)
                            ) {

                                option.selected = true;

                            }


                            subjectSelect.appendChild(
                                option
                            );

                        });


                    } catch (error) {

                        console.error(error);


                        subjectSelect.innerHTML =
                            '<option value="all">Gagal memuat mata pelajaran</option>';


                    } finally {

                        subjectSelect.disabled = false;

                    }

                }


                // ==========================================================
                // EVENT
                // ==========================================================

                academicYearSelect.addEventListener(
                    'change',
                    loadClasses
                );


                organizationSelect.addEventListener(
                    'change',
                    loadClasses
                );


                schoolClassSelect.addEventListener(
                    'change',
                    loadSubjects
                );


                // ==========================================================
                // LOAD AWAL
                // ==========================================================

                loadSubjects();

            });
        </script>
    @endpush
