@extends('adminlte::page')

@section('title', 'Student Attendance')

@section('content_header')

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">

        <div>
            <h1 class="mb-1">Student Attendance</h1>
            <p class="text-muted mb-0">
                Rekap kehadiran siswa berdasarkan minggu, mata pelajaran, dan pertemuan.
            </p>
        </div>

        <div class="d-flex flex-wrap gap-2">

            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#studentAttendanceFilterModal">

                <i class="bi bi-funnel me-1"></i>
                Filter

            </button>

            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#studentAttendanceSettingsModal">

                <i class="bi bi-gear me-1"></i>
                Pengaturan

            </button>

        </div>

    </div>

@stop


@section('content')

    {{-- Ringkasan Siswa --}}
    @include('admin.student-attendance.partials.summary')

    {{-- Chart Mingguan --}}
    <div class="card shadow-sm">

        <div class="card-header">

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">

                <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center gap-1 gap-md-3">

                    <h5 class="mb-0 font-weight-bold">
                        <i class="bi bi-calendar3 me-1"></i>
                        Student Attendance Mingguan
                    </h5>

                    <span class="text-muted small">
                        {{ $weekStart->translatedFormat('d M Y') }}
                        –
                        {{ $weekEnd->translatedFormat('d M Y') }}
                    </span>

                </div>

                <div class="btn-group">

                    {{-- Previous --}}
                    <a href="{{ request()->fullUrlWithQuery([
                        'week' => $weekStart->copy()->subWeek()->format('Y-m-d'),
                    ]) }}"
                        class="btn btn-outline-secondary btn-sm">

                        <i class="bi bi-chevron-left"></i>

                    </a>

                    {{-- Minggu Ini --}}
                    <a href="{{ request()->fullUrlWithQuery([
                        'week' => now()->format('Y-m-d'),
                    ]) }}"
                        class="btn btn-outline-primary btn-sm">

                        Minggu Ini

                    </a>

                    {{-- Next --}}
                    <a href="{{ request()->fullUrlWithQuery([
                        'week' => $weekStart->copy()->addWeek()->format('Y-m-d'),
                    ]) }}"
                        class="btn btn-outline-secondary btn-sm">

                        <i class="bi bi-chevron-right"></i>

                    </a>

                </div>

            </div>

        </div>

        <div class="card-body p-0">

            @include('admin.student-attendance.partials.weekly-chart')

        </div>

    </div>

@endsection


@include('admin.student-attendance.partials.filter-modal')
@include('admin.student-attendance.partials.settings-modal')
@include('admin.student-attendance.partials.detail-modal')


@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const academicYearSelect =
        document.getElementById('studentAttendanceAcademicYear');

    const organizationSelect =
        document.getElementById('studentAttendanceOrganization');

    const classSelect =
        document.getElementById('studentAttendanceSchoolClass');

    const studentSelect =
        document.getElementById('studentAttendanceStudent');

    const subjectSelect =
        document.getElementById('studentAttendanceSubject');


    if (!academicYearSelect ||
        !organizationSelect ||
        !classSelect ||
        !studentSelect) {

        return;
    }


    function loadFilterOptions() {

        const academicYearId =
            academicYearSelect.value;

        const organizationId =
            organizationSelect.value;

        const schoolClassId =
            classSelect.value;


        const params = new URLSearchParams({
            academic_year_id: academicYearId,
            organization_id: organizationId,
            school_class_id: schoolClassId,
        });


        /*
        |--------------------------------------------------------------------------
        | Loading
        |--------------------------------------------------------------------------
        */

        classSelect.disabled = true;
        studentSelect.disabled = true;

        if (subjectSelect) {
            subjectSelect.disabled = true;
        }


        classSelect.innerHTML =
            '<option value="all">Memuat kelas...</option>';

        studentSelect.innerHTML =
            '<option value="all">Memuat siswa...</option>';

        if (subjectSelect) {

            subjectSelect.innerHTML =
                '<option value="all">Memuat mata pelajaran...</option>';

        }


        /*
        |--------------------------------------------------------------------------
        | Request
        |--------------------------------------------------------------------------
        */

        fetch(
            `{{ route('admin.student-attendance.filter-options') }}?${params.toString()}`,
            {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            }
        )

        .then(response => {

            if (!response.ok) {
                throw new Error(
                    'Gagal mengambil data filter.'
                );
            }

            return response.json();

        })

        .then(data => {


            /*
            |--------------------------------------------------------------------------
            | Kelas
            |--------------------------------------------------------------------------
            */

            classSelect.innerHTML =
                '<option value="all">Semua Kelas</option>';

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
            |--------------------------------------------------------------------------
            | Siswa
            |--------------------------------------------------------------------------
            */

            studentSelect.innerHTML =
                '<option value="all">Semua Siswa</option>';

            if (data.students) {

                Object.entries(data.students).forEach(
                    ([id, name]) => {

                        const option =
                            document.createElement('option');

                        option.value = id;
                        option.textContent = name;

                        studentSelect.appendChild(option);

                    }
                );

            }


            /*
            |--------------------------------------------------------------------------
            | Mata Pelajaran
            |--------------------------------------------------------------------------
            */

            if (subjectSelect) {

                subjectSelect.innerHTML =
                    '<option value="all">Semua Mata Pelajaran</option>';

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


            /*
            |--------------------------------------------------------------------------
            | Aktifkan kembali
            |--------------------------------------------------------------------------
            */

            classSelect.disabled = false;
            studentSelect.disabled = false;

            if (subjectSelect) {
                subjectSelect.disabled = false;
            }

        })

        .catch(error => {

            console.error(error);

            classSelect.innerHTML =
                '<option value="all">Gagal memuat kelas</option>';

            studentSelect.innerHTML =
                '<option value="all">Gagal memuat siswa</option>';

            if (subjectSelect) {

                subjectSelect.innerHTML =
                    '<option value="all">Gagal memuat mata pelajaran</option>';

            }

            classSelect.disabled = false;
            studentSelect.disabled = false;

            if (subjectSelect) {
                subjectSelect.disabled = false;
            }

        });

    }


    /*
    |--------------------------------------------------------------------------
    | Tahun Akademik berubah
    |--------------------------------------------------------------------------
    */

    academicYearSelect.addEventListener(
        'change',
        function () {

            loadFilterOptions();

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Unit berubah
    |--------------------------------------------------------------------------
    */

    organizationSelect.addEventListener(
        'change',
        function () {

            loadFilterOptions();

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Kelas berubah
    |--------------------------------------------------------------------------
    */

    classSelect.addEventListener(
        'change',
        function () {

            loadFilterOptions();

        }
    );

});
</script>
@endpush


@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const detailModal =
        document.getElementById('studentAttendanceDetailModal');

    if (!detailModal) {
        return;
    }


    /*
    |--------------------------------------------------------------------------
    | Elemen detail
    |--------------------------------------------------------------------------
    */

    const studentName =
        document.getElementById('detailStudentName');

    const date =
        document.getElementById('detailDate');

    const meetingNumber =
        document.getElementById('detailMeetingNumber');

    const subject =
        document.getElementById('detailSubject');

    const teacher =
        document.getElementById('detailTeacher');

    const organization =
        document.getElementById('detailOrganization');

    const schoolClass =
        document.getElementById('detailSchoolClass');

    const status =
        document.getElementById('detailStatus');

    const notes =
        document.getElementById('detailNotes');


    /*
    |--------------------------------------------------------------------------
    | Ketika modal akan dibuka
    |--------------------------------------------------------------------------
    */

    detailModal.addEventListener(
        'show.bs.modal',
        function (event) {

            const button = event.relatedTarget;

            if (!button) {
                return;
            }


            /*
            |--------------------------------------------------------------------------
            | Ambil data dari tombol
            |--------------------------------------------------------------------------
            */

            const student =
                button.dataset.student || '-';

            const attendanceDate =
                button.dataset.date || '-';

            const meeting =
                button.dataset.meeting || '-';

            const attendanceSubject =
                button.dataset.subject || '-';

            const attendanceTeacher =
                button.dataset.teacher || '-';

            const attendanceOrganization =
                button.dataset.organization || '-';

            const attendanceClass =
                button.dataset.class || '-';

            const attendanceStatus =
                button.dataset.status || '-';

            const attendanceNotes =
                button.dataset.notes || '-';


            /*
            |--------------------------------------------------------------------------
            | Tampilkan data
            |--------------------------------------------------------------------------
            */

            studentName.textContent =
                student;

            date.textContent =
                attendanceDate;

            meetingNumber.textContent =
                meeting;

            subject.textContent =
                attendanceSubject;

            teacher.textContent =
                attendanceTeacher;

            organization.textContent =
                attendanceOrganization;

            schoolClass.textContent =
                attendanceClass;

            status.textContent =
                attendanceStatus;

            notes.textContent =
                attendanceNotes || '-';


            /*
            |--------------------------------------------------------------------------
            | Warna status
            |--------------------------------------------------------------------------
            */

            status.className =
                'badge';


            switch (attendanceStatus.toLowerCase()) {

                case 'hadir':
                    status.classList.add('bg-success');
                    break;

                case 'izin':
                    status.classList.add('bg-warning', 'text-dark');
                    break;

                case 'sakit':
                    status.classList.add('bg-info');
                    break;

                case 'alpa':
                    status.classList.add('bg-danger');
                    break;

                default:
                    status.classList.add('bg-secondary');
                    break;

            }

        }
    );

});
</script>
@endpush
