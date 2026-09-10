<div class="card card-primary">

    <div class="card-header">
        <h3 class="card-title">
            <i class="bi bi-funnel me-1"></i>
            Filter Laporan
        </h3>
    </div>

    <div class="card-body">

        <form method="GET" action="{{ route('admin.reports.students.index') }}">

            <div class="row">

                {{-- Tahun Ajaran --}}
                <div class="col-md-3 mb-3">
                    <label for="academic_year_id">
                        Tahun Ajaran
                    </label>

                    <select name="academic_year_id" id="academic_year_id" class="form-control">
                        <option value="">
                            Semua Tahun Ajaran
                        </option>

                        @foreach ($academicYears as $academicYear)
                            <option value="{{ $academicYear->id }}" @selected($academicYearId == $academicYear->id)>
                                {{ $academicYear->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Unit --}}
                <div class="col-md-3 mb-3">
                    <label for="organization_id">
                        Unit
                    </label>

                    <select name="organization_id" id="organization_id" class="form-control">
                        <option value="">
                            Semua Unit
                        </option>

                        @foreach ($organizations as $organization)
                            <option value="{{ $organization->id }}" @selected($organizationId == $organization->id)>
                                {{ $organization->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Kelas --}}
                <div class="col-md-3 mb-3">
                    <label for="school_class_id">
                        Kelas
                    </label>

                    <select name="school_class_id" id="school_class_id" class="form-control">
                        <option value="">
                            Semua Kelas
                        </option>

                        @foreach ($schoolClasses as $schoolClass)
                            <option value="{{ $schoolClass->id }}" @selected($schoolClassId == $schoolClass->id)>
                                {{ $schoolClass->name }}
                                —
                                {{ $schoolClass->organization->name ?? '-' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Status --}}
                <div class="col-md-3 mb-3">
                    <label for="status">
                        Status Siswa
                    </label>

                    <select name="status" id="status" class="form-control">
                        <option value="">
                            Semua Status
                        </option>

                        <option value="active" @selected($status === 'active')>
                            Aktif
                        </option>

                        <option value="inactive" @selected($status === 'inactive')>
                            Tidak Aktif
                        </option>
                    </select>
                </div>

            </div>

            <div class="d-flex gap-2">

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search me-1"></i>
                    Tampilkan
                </button>

                <a href="{{ route('admin.reports.students.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-counterclockwise me-1"></i>
                    Reset
                </a>

            </div>

        </form>

    </div>

</div>


@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const academicYearSelect = document.getElementById('academic_year_id');
            const organizationSelect = document.getElementById('organization_id');
            const schoolClassSelect = document.getElementById('school_class_id');

            const selectedClassId = @json($schoolClassId);

            async function loadClasses() {

                const academicYearId = academicYearSelect.value;
                const organizationId = organizationSelect.value;

                schoolClassSelect.innerHTML = '';

                const allOption = document.createElement('option');
                allOption.value = '';
                allOption.textContent = 'Semua Kelas';

                schoolClassSelect.appendChild(allOption);

                const params = new URLSearchParams();

                if (academicYearId) {
                    params.append(
                        'academic_year_id',
                        academicYearId
                    );
                }

                if (organizationId) {
                    params.append(
                        'organization_id',
                        organizationId
                    );
                }

                try {

                    schoolClassSelect.disabled = true;

                    const response = await fetch(
                        `{{ route('admin.reports.students.classes') }}?${params.toString()}`, {
                            headers: {
                                'Accept': 'application/json'
                            }
                        }
                    );

                    if (!response.ok) {
                        throw new Error('Gagal mengambil data kelas.');
                    }

                    const classes = await response.json();

                    classes.forEach(function(schoolClass) {

                        const option = document.createElement('option');

                        option.value = schoolClass.id;

                        option.textContent = schoolClass.name;

                        if (
                            selectedClassId &&
                            Number(selectedClassId) === Number(schoolClass.id)
                        ) {
                            option.selected = true;
                        }

                        schoolClassSelect.appendChild(option);
                    });

                } catch (error) {

                    console.error(error);

                } finally {

                    schoolClassSelect.disabled = false;
                }
            }

            academicYearSelect.addEventListener(
                'change',
                function() {

                    loadClasses();

                }
            );

            organizationSelect.addEventListener(
                'change',
                function() {

                    loadClasses();

                }
            );

        });
    </script>
@endpush
