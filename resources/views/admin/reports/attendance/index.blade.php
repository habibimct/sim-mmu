@extends('adminlte::page')

@section('title', 'Laporan Absensi')

@section('content_header')

    <div class="d-flex justify-content-between align-items-center">

        <div>
            <h1 class="m-0">
                <i class="bi bi-calendar-check me-1"></i>
                Laporan Absensi
            </h1>

            <small class="text-muted">
                Laporan absensi guru dan siswa
            </small>
        </div>

    </div>

@stop


@section('content')

    {{-- ==========================================================
    TAB
    =========================================================== --}}

    <div class="card">

        <div class="card-header p-0">

            <ul class="nav nav-tabs">

                <li class="nav-item">

                    <a
                        href="{{ request()->fullUrlWithQuery([
                            'tab' => 'guru',
                        ]) }}"
                        class="nav-link {{ $tab === 'guru' ? 'active' : '' }}"
                    >
                        <i class="bi bi-person-workspace me-1"></i>
                        Absensi Guru
                    </a>

                </li>

                <li class="nav-item">

                    <a
                        href="{{ request()->fullUrlWithQuery([
                            'tab' => 'siswa',
                        ]) }}"
                        class="nav-link {{ $tab === 'siswa' ? 'active' : '' }}"
                    >
                        <i class="bi bi-people me-1"></i>
                        Absensi Siswa
                    </a>

                </li>

            </ul>

        </div>


        <div class="card-body">

            @if ($tab === 'guru')

                @include(
                    'admin.reports.attendance.partials.teacher',
                    [
                        'tab' => $tab,
                        'academicYears' => $academicYears,
                        'academicYearId' => $academicYearId,
                        'organizations' => $organizations,
                        'organizationId' => $organizationId,
                        'schoolClasses' => $schoolClasses,
                        'schoolClassId' => $schoolClassId,
                        'subjects' => $subjects,
                        'subjectId' => $subjectId,
                        'week' => $week,
                        'weekStart' => $weekStart,
                        'weekEnd' => $weekEnd,
                        'weeklyAttendances' => $weeklyAttendances,
                        'teacherAttendanceHours' => $teacherAttendanceHours,
                        'teacherAttendanceInterval' => $teacherAttendanceInterval,
                    ]
                )

            @else

                @include(
                    'admin.reports.attendance.partials.student',
                    [
                        'tab' => $tab,
                        'academicYears' => $academicYears,
                        'academicYearId' => $academicYearId,
                        'organizations' => $organizations,
                        'organizationId' => $organizationId,
                        'schoolClasses' => $schoolClasses,
                        'schoolClassId' => $schoolClassId,
                        'month' => $month,
                        'year' => $year,
                        'monthStart' => $monthStart,
                        'monthEnd' => $monthEnd,
                        'studentAcademicYears' => $studentAcademicYears,
                        'attendanceDetails' => $attendanceDetails,
                    ]
                )

            @endif

        </div>

    </div>

@stop
