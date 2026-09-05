<div class="card mb-3">

    <div class="card-body">

        <div class="d-flex justify-content-between align-items-center">

            <div class="text-muted small">

                @if (request()->hasAny(['search', 'status', 'school_class_id', 'subject_id']))
                    <i class="bi bi-funnel-fill me-1"></i>
                    Filter sedang digunakan
                @else
                    <i class="bi bi-list-ul me-1"></i>
                    Daftar penugasan mengajar
                @endif

            </div>


            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal"
                data-bs-target="#modalTeachingAssignmentFilter">

                <i class="bi bi-funnel me-1"></i>

                Filter

                @if (request()->hasAny(['search', 'academic_year_id', 'status', 'school_class_id', 'subject_id']))
                    <span class="badge text-bg-danger ms-1">
                        Aktif
                    </span>
                @endif

            </button>

        </div>

    </div>

</div>


{{-- =========================================================
MODAL FILTER
========================================================= --}}

<div class="modal fade" id="modalTeachingAssignmentFilter" tabindex="-1" aria-hidden="true">

    <div class="modal-dialog">

        <div class="modal-content">

            <form method="GET" action="{{ route('admin.teaching-assignments.index') }}">

                <div class="modal-header">

                    <h5 class="modal-title">

                        <i class="bi bi-funnel me-1"></i>

                        Filter Penugasan

                    </h5>

                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>

                </div>


                <div class="modal-body">

                    {{-- Pencarian --}}

                    <div class="mb-3">

                        <label class="form-label">
                            Pencarian
                        </label>

                        <input type="text" name="search" class="form-control" value="{{ request('search') }}"
                            placeholder="Guru, NIK, kelas, atau mata pelajaran">

                    </div>


                    {{-- Tahun Ajaran --}}

                    <div class="mb-3">

                        <label class="form-label">
                            Tahun Ajaran
                        </label>

                        <select name="academic_year_id" id="filterAcademicYear" class="form-select">

                            <option value="">
                                Semua Tahun Ajaran
                            </option>

                            @foreach ($academicYears as $academicYear)
                                <option value="{{ $academicYear->id }}" @selected((string) request('academic_year_id') === (string) $academicYear->id)>

                                    {{ $academicYear->name }}

                                </option>
                            @endforeach

                        </select>

                    </div>


                    {{-- Kelas --}}

                    <div class="mb-3">

                        <label class="form-label">
                            Kelas
                        </label>

                        <select name="school_class_id" id="filterSchoolClass" class="form-select">

                            <option value="">
                                Semua Kelas
                            </option>

                            @foreach ($schoolClasses as $class)
                                <option value="{{ $class->id }}" data-academic-year="{{ $class->academic_year_id }}" @selected((string) request('school_class_id') === (string) $class->id)>

                                    {{ $class->name }}

                                    @if ($class->academicYear)
                                        — {{ $class->academicYear->name }}
                                    @endif

                                </option>
                            @endforeach

                        </select>

                    </div>


                    {{-- Mata Pelajaran --}}

                    <div class="mb-3">

                        <label class="form-label">
                            Mata Pelajaran
                        </label>

                        <select name="subject_id" class="form-select">

                            <option value="">
                                Semua Mata Pelajaran
                            </option>

                            @foreach ($subjects as $subject)
                                <option value="{{ $subject->id }}" @selected((string) request('subject_id') === (string) $subject->id)>

                                    {{ $subject->name }}

                                    @if (!empty($subject->code))
                                        ({{ $subject->code }})
                                    @endif

                                </option>
                            @endforeach

                        </select>

                    </div>


                    {{-- Status --}}

                    <div class="mb-3">

                        <label class="form-label">
                            Status
                        </label>

                        <select name="status" class="form-select">

                            <option value="">
                                Semua Status
                            </option>

                            <option value="active" @selected(request('status') === 'active')>
                                Aktif
                            </option>

                            <option value="inactive" @selected(request('status') === 'inactive')>
                                Tidak Aktif
                            </option>

                        </select>

                    </div>

                </div>


                <div class="modal-footer">

                    <a href="{{ route('admin.teaching-assignments.index') }}" class="btn btn-light">
                        Reset
                    </a>

                    <button type="submit" class="btn btn-primary">

                        <i class="bi bi-search me-1"></i>

                        Terapkan Filter

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>


@push('js')

<script>

document.addEventListener('DOMContentLoaded', function () {

    const academicYear =
        document.getElementById('filterAcademicYear');

    const schoolClass =
        document.getElementById('filterSchoolClass');


    if (!academicYear || !schoolClass) {
        return;
    }


    function filterClasses() {

        const yearId = academicYear.value;

        const options =
            schoolClass.querySelectorAll('option');


        options.forEach(function (option) {

            // "Semua Kelas" selalu ditampilkan
            if (option.value === '') {

                option.hidden = false;

                return;
            }


            const optionYear =
                option.dataset.academicYear;


            if (!yearId || optionYear === yearId) {

                option.hidden = false;

            } else {

                option.hidden = true;

                // Jika kelas yang sedang dipilih
                // bukan bagian dari tahun tersebut,
                // kosongkan pilihan.
                if (schoolClass.value === option.value) {

                    schoolClass.value = '';

                }

            }

        });

    }


    academicYear.addEventListener(
        'change',
        filterClasses
    );


    // Jalankan saat modal/filter pertama kali dibuka
    filterClasses();

});

</script>

@endpush
