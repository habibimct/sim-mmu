{{-- =========================================================
MODAL FILTER TAGIHAN
========================================================= --}}

<div class="modal fade" id="modalFilterStudentBills" tabindex="-1" aria-labelledby="modalFilterStudentBillsLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content">

            {{-- Header --}}

            <div class="modal-header">

                <div>

                    <h5 class="modal-title" id="modalFilterStudentBillsLabel">
                        Filter Tagihan
                    </h5>

                    <small class="text-muted">
                        Gunakan filter untuk mempersempit daftar tagihan.
                    </small>

                </div>

                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

            </div>


            {{-- Form --}}

            <form method="GET" action="{{ route('admin.finance.bills.index') }}">

                <div class="modal-body">

                    <div class="row g-3">

                        {{-- =================================================
                        ORGANISASI
                        ================================================== --}}

                        <div class="col-md-6">

                            <label for="filter_organization_id" class="form-label">
                                Organisasi / Unit
                            </label>

                            <select name="organization_id" id="filter_organization_id" class="form-select">

                                <option value="">
                                    Semua Unit
                                </option>

                                @foreach ($organizations as $organization)
                                    <option value="{{ $organization->id }}" @selected((string) $organizationId === (string) $organization->id)>
                                        {{ $organization->name }}
                                    </option>
                                @endforeach

                            </select>

                        </div>


                        {{-- =================================================
                        TAHUN AKADEMIK
                        ================================================== --}}

                        <div class="col-md-6">

                            <label for="filter_academic_year_id" class="form-label">
                                Tahun Akademik
                            </label>

                            <select name="academic_year_id" id="filter_academic_year_id" class="form-select">

                                <option value="">
                                    Semua Tahun
                                </option>

                                @foreach ($academicYears as $academicYear)
                                    <option value="{{ $academicYear->id }}" @selected((string) $academicYearId === (string) $academicYear->id)>
                                        {{ $academicYear->name }}
                                    </option>
                                @endforeach

                            </select>

                        </div>


                        {{-- =================================================
                        KELAS
                        ================================================== --}}

                        <div class="col-md-6">

                            <label for="filter_school_class_id" class="form-label">
                                Kelas
                            </label>

                            <select name="school_class_id" id="filter_school_class_id" class="form-select">

                                <option value="">
                                    Semua Kelas
                                </option>

                                @foreach ($schoolClasses as $schoolClass)
                                    <option value="{{ $schoolClass->id }}"
                                        data-organization-id="{{ $schoolClass->organization_id }}"
                                        data-academic-year-id="{{ $schoolClass->academic_year_id }}"
                                        @selected((string) $schoolClassId === (string) $schoolClass->id)>
                                        {{ $schoolClass->name }}
                                    </option>
                                @endforeach

                            </select>

                        </div>


                        {{-- =================================================
                        JENIS TAGIHAN
                        ================================================== --}}

                        <div class="col-md-6">

                            <label for="filter_bill_type_id" class="form-label">
                                Jenis Tagihan
                            </label>

                            <select name="bill_type_id" id="filter_bill_type_id" class="form-select">

                                <option value="">
                                    Semua Jenis
                                </option>

                                @foreach ($billTypes as $billType)
                                    <option value="{{ $billType->id }}" @selected((string) $billTypeId === (string) $billType->id)>
                                        {{ $billType->name }}
                                    </option>
                                @endforeach

                            </select>

                        </div>


                        {{-- =================================================
                        STATUS
                        ================================================== --}}

                        <div class="col-md-6">

                            <label for="filter_status" class="form-label">
                                Status
                            </label>

                            <select name="status" id="filter_status" class="form-select">

                                <option value="">
                                    Semua Status
                                </option>

                                <option value="unpaid" @selected($status === 'unpaid')>
                                    Belum Bayar
                                </option>

                                <option value="partial" @selected($status === 'partial')>
                                    Sebagian
                                </option>

                                <option value="paid" @selected($status === 'paid')>
                                    Lunas
                                </option>

                                <option value="cancelled" @selected($status === 'cancelled')>
                                    Dibatalkan
                                </option>

                            </select>

                        </div>


                        {{-- =================================================
                        KODE / NAMA SISWA
                        ================================================== --}}

                        <div class="col-md-6">

                            <label for="filter_student" class="form-label">
                                Kode / Nama Siswa
                            </label>

                            <input type="text" name="student" id="filter_student" value="{{ $student }}"
                                class="form-control" placeholder="NIS atau nama siswa...">

                        </div>

                    </div>

                </div>


                {{-- Footer --}}

                <div class="modal-footer">

                    <a href="{{ route('admin.finance.bills.index') }}" class="btn btn-light border">
                        Reset
                    </a>

                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Batal
                    </button>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-funnel me-1"></i>
                        Terapkan
                    </button>

                </div>

            </form>

        </div>

    </div>
</div>


@push('js')

<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {

        const organization =
            document.getElementById(
                'filter_organization_id'
            );

        const academicYear =
            document.getElementById(
                'filter_academic_year_id'
            );

        const schoolClass =
            document.getElementById(
                'filter_school_class_id'
            );


        if (
            !organization ||
            !academicYear ||
            !schoolClass
        ) {
            return;
        }


        function filterClasses() {

            const organizationId =
                organization.value;

            const academicYearId =
                academicYear.value;


            let selectedStillVisible = false;


            Array.from(
                schoolClass.options
            ).forEach(function (option) {

                /*
                |--------------------------------------------------------------------------
                | "Semua Kelas"
                |--------------------------------------------------------------------------
                */

                if (!option.value) {

                    option.hidden = false;

                    return;
                }


                const optionOrganizationId =
                    option.dataset.organizationId;

                const optionAcademicYearId =
                    option.dataset.academicYearId;


                /*
                |--------------------------------------------------------------------------
                | Cocokkan organisasi
                |--------------------------------------------------------------------------
                */

                const organizationMatch =
                    !organizationId ||
                    optionOrganizationId ===
                    organizationId;


                /*
                |--------------------------------------------------------------------------
                | Cocokkan tahun akademik
                |--------------------------------------------------------------------------
                */

                const academicYearMatch =
                    !academicYearId ||
                    optionAcademicYearId ===
                    academicYearId;


                const visible =
                    organizationMatch &&
                    academicYearMatch;


                option.hidden =
                    !visible;


                if (
                    visible &&
                    option.selected
                ) {
                    selectedStillVisible = true;
                }

            });


            /*
            |--------------------------------------------------------------------------
            | Kalau kelas yang sedang dipilih tidak lagi sesuai,
            | kembali ke "Semua Kelas"
            |--------------------------------------------------------------------------
            */

            if (
                !selectedStillVisible
            ) {
                schoolClass.value = '';
            }

        }


        /*
        |--------------------------------------------------------------------------
        | Organisasi berubah
        |--------------------------------------------------------------------------
        */

        organization.addEventListener(
            'change',
            function () {

                filterClasses();

            }
        );


        /*
        |--------------------------------------------------------------------------
        | Tahun Akademik berubah
        |--------------------------------------------------------------------------
        */

        academicYear.addEventListener(
            'change',
            function () {

                filterClasses();

            }
        );


        /*
        |--------------------------------------------------------------------------
        | Kondisi awal
        |--------------------------------------------------------------------------
        */

        filterClasses();

    });

</script>

@endpush
