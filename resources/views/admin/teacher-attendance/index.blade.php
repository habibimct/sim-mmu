@extends('adminlte::page')

@section('title', 'Jadwal')


{{-- =========================================================
    CONTENT HEADER
========================================================= --}}
@section('content_header')

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">

        <div>
            <h1 class="mb-1">
                Absensi Guru
            </h1>

            <p class="text-muted mb-0">
                Tampilan berdasarkan data absensi guru.
            </p>
        </div>


        <div class="d-flex flex-wrap gap-2">

            {{-- Filter --}}
            <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal"
                data-bs-target="#teacherAttendanceFilterModal">

                <i class="bi bi-funnel me-1"></i>
                Filter

            </button>


            {{-- Pengaturan --}}
            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                data-bs-target="#teacherAttendanceSettingsModal">

                <i class="bi bi-gear me-1"></i>
                Pengaturan

            </button>

        </div>

    </div>

@stop


{{-- =========================================================
    CONTENT
========================================================= --}}
@section('content')


    {{-- =====================================================
        FILTER AKTIF
    ====================================================== --}}
    <div class="mb-3">

        {{-- Tahun Akademik --}}
        @if ($academicYearId === 'all')

            <span class="badge bg-primary me-1">
                Semua Tahun Akademik
            </span>
        @else
            @php
                $selectedAcademicYear = $academicYears->firstWhere('id', $academicYearId);
            @endphp

            @if ($selectedAcademicYear)
                <span class="badge bg-primary me-1">
                    {{ $selectedAcademicYear->name }}
                </span>
            @endif

        @endif


        {{-- Unit --}}
        @if ($organizationId && $organizationId !== 'all')
            @php
                $selectedOrganization = $organizationOptions->get($organizationId);
            @endphp

            <span class="badge bg-info me-1">
                Unit: {{ $selectedOrganization }}
            </span>
        @else
            <span class="badge bg-secondary me-1">
                Semua Unit
            </span>
        @endif


        {{-- Kelas --}}
        @if ($schoolClassId && $schoolClassId !== 'all')
            @php
                $selectedClass = $classOptions->get($schoolClassId);
            @endphp

            <span class="badge bg-info me-1">
                Kelas: {{ $selectedClass }}
            </span>
        @else
            <span class="badge bg-secondary me-1">
                Semua Kelas
            </span>
        @endif


        {{-- Mata Pelajaran --}}
        @if ($subjectId && $subjectId !== 'all')
            @php
                $selectedSubject = $subjectOptions->get($subjectId);
            @endphp

            <span class="badge bg-info me-1">
                Mapel: {{ $selectedSubject }}
            </span>
        @else
            <span class="badge bg-secondary me-1">
                Semua Mapel
            </span>
        @endif

    </div>


    {{-- =====================================================
        JADWAL MINGGUAN
    ====================================================== --}}
    <div class="card shadow-sm">

        {{-- Card Header --}}
        <div class="card-header">

            <div
                class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">

                {{-- Judul dan Periode --}}
                <div class="d-flex flex-column flex-md-row align-items-md-center gap-1 gap-md-3">

                    <h5 class="mb-0 font-weight-bold">

                        <i class="bi bi-calendar3 me-1"></i>
                        Jadwal Mingguan

                    </h5>

                    <span class="text-muted small">

                        {{ $weekStart->translatedFormat('d M Y') }}
                        –
                        {{ $weekEnd->translatedFormat('d M Y') }}

                    </span>

                </div>


                {{-- Navigasi Minggu --}}
                <div class="btn-group flex-shrink-0">

                    {{-- Previous --}}
                    <a href="{{ request()->fullUrlWithQuery([
                        'week' => $weekStart->copy()->subWeek()->format('Y-m-d'),
                    ]) }}"
                        class="btn btn-sm btn-outline-secondary" title="Minggu sebelumnya">

                        <i class="bi bi-chevron-left"></i>

                    </a>


                    {{-- Minggu Ini --}}
                    <a href="{{ request()->fullUrlWithQuery([
                        'week' => now()->format('Y-m-d'),
                    ]) }}"
                        class="btn btn-sm btn-outline-primary">

                        Minggu Ini

                    </a>


                    {{-- Next --}}
                    <a href="{{ request()->fullUrlWithQuery([
                        'week' => $weekStart->copy()->addWeek()->format('Y-m-d'),
                    ]) }}"
                        class="btn btn-sm btn-outline-secondary" title="Minggu berikutnya">

                        <i class="bi bi-chevron-right"></i>

                    </a>

                </div>

            </div>

        </div>
        {{-- =================================================
            CHART
        ================================================== --}}
        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-bordered table-hover mb-0">

                    {{-- Header --}}
                    <thead class="table-light">

                        <tr>

                            <th style="width: 80px;" class="text-center align-middle">

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


                    {{-- Body --}}
                    <tbody>

                        @forelse ($teacherAttendanceHours as $hour)

                            <tr>

                                {{-- Jam --}}
                                <td class="text-center align-top fw-bold text-muted">

                                    {{ $hour }}

                                </td>


                                {{-- Hari --}}
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


                                    <td class="align-top" style="min-width: 150px; height: 75px;">

                                        @forelse ($cellAttendances as $attendance)
                                            <button type="button"
                                                class="w-100 text-start p-2 mb-1 rounded bg-light border btn btn-link text-decoration-none text-dark"
                                                data-bs-toggle="modal" data-bs-target="#teacherAttendanceDetailModal"
                                                data-attendance-id="{{ $attendance->id }}">

                                                {{-- Kelas --}}
                                                <div class="fw-bold text-primary">

                                                    {{ $attendance->teachingAssignment->schoolClass->name }}

                                                </div>


                                                {{-- Mata Pelajaran --}}
                                                <div class="small">

                                                    {{ $attendance->teachingAssignment->subject->name }}

                                                </div>


                                                {{-- Pertemuan --}}
                                                <div class="small text-muted">

                                                    Pertemuan ke-
                                                    {{ $attendance->meeting_number }}

                                                </div>


                                                {{-- Guru --}}
                                                <div class="small text-muted">

                                                    {{ $attendance->teachingAssignment->teacher->name }}

                                                </div>


                                                {{-- Jam Input --}}
                                                <div class="small text-muted">

                                                    Input
                                                    {{ $attendance->created_at->format('H:i') }}

                                                </div>

                                                @php
                                                    $studentAttendanceData = $attendance->details
                                                        ->map(function ($detail) {
                                                            return [
                                                                'nis' => $detail->studentAcademicYear?->student?->nis,
                                                                'name' => $detail->studentAcademicYear?->student?->name,
                                                                'status' => $detail->status,
                                                                'notes' => $detail->notes,
                                                            ];
                                                        })
                                                        ->values();
                                                @endphp

                                            </button>

                                        @empty

                                            <div style="min-height: 50px;"></div>
                                        @endforelse

                                    </td>
                                @endforeach

                            </tr>

                            @empty

                                <tr>

                                    <td colspan="8" class="text-center text-muted py-5">

                                        <i class="far fa-calendar-times fa-2x mb-2"></i>

                                        <div>
                                            Belum ada data absensi pada minggu ini.
                                        </div>

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>


        {{-- =====================================================
            MODAL FILTER
        ====================================================== --}}
        @include('admin.teacher-attendance.partials.filter-modal')


        {{-- =====================================================
            MODAL PENGATURAN
        ====================================================== --}}
        @include('admin.teacher-attendance.partials.settings-modal')

        @include('admin.teacher-attendance.partials.detail-modal')


    @endsection


    @push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const detailModal =
        document.getElementById('teacherAttendanceDetailModal');

    if (!detailModal) {
        return;
    }

    detailModal.addEventListener(
        'show.bs.modal',
        async function (event) {

            const button = event.relatedTarget;

            if (!button) {
                return;
            }

            const attendanceId =
                button.dataset.attendanceId;

            if (!attendanceId) {
                return;
            }

            // Reset tampilan
            document.getElementById(
                'teacherAttendanceStudentRows'
            ).innerHTML = `
                <tr>
                    <td colspan="5"
                        class="text-center text-muted py-4">
                        Memuat data siswa...
                    </td>
                </tr>
            `;

            document.getElementById(
                'detailCountPresent'
            ).textContent = '0';

            document.getElementById(
                'detailCountSick'
            ).textContent = '0';

            document.getElementById(
                'detailCountPermission'
            ).textContent = '0';

            document.getElementById(
                'detailCountAbsent'
            ).textContent = '0';

            try {

                const response = await fetch(
                    `{{ url('/admin/teacher-attendance') }}/${attendanceId}/detail`,
                    {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    }
                );

                if (!response.ok) {
                    throw new Error(
                        'HTTP ' + response.status
                    );
                }

                const data = await response.json();

                // ==========================
                // DATA ABSENSI
                // ==========================

                document.getElementById(
                    'detailTeacherName'
                ).textContent =
                    data.teacher || '-';

                document.getElementById(
                    'detailOrganizationName'
                ).textContent =
                    data.organization || '-';

                document.getElementById(
                    'detailClassName'
                ).textContent =
                    data.class || '-';

                document.getElementById(
                    'detailSubjectName'
                ).textContent =
                    data.subject || '-';

                document.getElementById(
                    'detailAttendanceDate'
                ).textContent =
                    data.attendance?.date || '-';

                document.getElementById(
                    'detailMeetingNumber'
                ).textContent =
                    'Pertemuan ke-' +
                    (data.attendance?.meeting_number || '-');

                document.getElementById(
                    'detailAttendanceTime'
                ).textContent =
                    data.attendance?.time || '-';

                document.getElementById(
                    'teacherAttendanceDetailSubtitle'
                ).textContent =
                    (data.class || '-') +
                    ' • ' +
                    (data.subject || '-') +
                    ' • ' +
                    (data.attendance?.date || '-');


                // ==========================
                // DATA SISWA
                // ==========================

                const students =
                    data.students || [];

                let present = 0;
                let sick = 0;
                let permission = 0;
                let absent = 0;

                students.forEach(function (student) {

                    switch (student.status) {

                        case 'present':
                            present++;
                            break;

                        case 'sick':
                            sick++;
                            break;

                        case 'permission':
                            permission++;
                            break;

                        case 'absent':
                            absent++;
                            break;
                    }
                });

                document.getElementById(
                    'detailCountPresent'
                ).textContent = present;

                document.getElementById(
                    'detailCountSick'
                ).textContent = sick;

                document.getElementById(
                    'detailCountPermission'
                ).textContent = permission;

                document.getElementById(
                    'detailCountAbsent'
                ).textContent = absent;


                // ==========================
                // TABEL SISWA
                // ==========================

                const rows =
                    document.getElementById(
                        'teacherAttendanceStudentRows'
                    );

                if (!students.length) {

                    rows.innerHTML = `
                        <tr>
                            <td colspan="5"
                                class="text-center text-muted py-4">
                                Belum ada data kehadiran siswa.
                            </td>
                        </tr>
                    `;

                    return;
                }

                rows.innerHTML = '';

                students.forEach(function (
                    student,
                    index
                ) {

                    let statusLabel = '-';
                    let statusClass = 'bg-secondary';

                    switch (student.status) {

                        case 'present':
                            statusLabel = 'Hadir';
                            statusClass = 'bg-success';
                            break;

                        case 'sick':
                            statusLabel = 'Sakit';
                            statusClass = 'bg-info';
                            break;

                        case 'permission':
                            statusLabel = 'Izin';
                            statusClass =
                                'bg-warning text-dark';
                            break;

                        case 'absent':
                            statusLabel = 'Alpa';
                            statusClass = 'bg-danger';
                            break;
                    }

                    const row =
                        document.createElement('tr');

                    row.innerHTML = `
                        <td class="text-center">
                            ${index + 1}
                        </td>

                        <td>
                            ${escapeHtml(
                                student.nis || '-'
                            )}
                        </td>

                        <td class="fw-semibold">
                            ${escapeHtml(
                                student.name || '-'
                            )}
                        </td>

                        <td class="text-center">
                            <span class="badge ${statusClass}">
                                ${statusLabel}
                            </span>
                        </td>

                        <td>
                            ${escapeHtml(
                                student.notes || '-'
                            )}
                        </td>
                    `;

                    rows.appendChild(row);
                });

            } catch (error) {

                console.error(
                    'Gagal mengambil detail absensi:',
                    error
                );

                document.getElementById(
                    'teacherAttendanceStudentRows'
                ).innerHTML = `
                    <tr>
                        <td colspan="5"
                            class="text-center text-danger py-4">
                            Gagal memuat data kehadiran siswa.
                        </td>
                    </tr>
                `;
            }
        }
    );


    function escapeHtml(value) {

        const div =
            document.createElement('div');

        div.textContent =
            value ?? '';

        return div.innerHTML;
    }

});
</script>
@endpush
