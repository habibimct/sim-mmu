<div class="card border-0 shadow-sm mb-4">

    {{-- Header --}}
    <div class="card-header bg-white border-bottom py-3">

        <div class="d-flex align-items-center">

            <div class="bg-primary bg-opacity-10 rounded-3 p-2 me-3">
                <i class="bi bi-funnel-fill text-primary fs-5"></i>
            </div>

            <div>
                <h5 class="mb-0 fw-semibold">
                    Filter Laporan
                </h5>

                <small class="text-muted">
                    Tentukan kriteria laporan tagihan
                </small>
            </div>

        </div>

    </div>


    {{-- Body --}}
    <div class="card-body p-4">

        <form
            method="GET"
            action="{{ route('admin.reports.bills.index') }}"
        >

            <div class="row g-3">

                {{-- Tahun Ajaran --}}
                <div class="col-md-6 col-lg-2">

                    <label
                        for="academic_year_id"
                        class="form-label fw-semibold"
                    >
                        Tahun Ajaran
                    </label>

                    <select
                        id="academic_year_id"
                        name="academic_year_id"
                        class="form-select"
                    >

                        <option
                            value="all"
                            @selected($academicYearId === '' || $academicYearId === 'all')
                        >
                            Semua Tahun Ajaran
                        </option>

                        @foreach ($academicYears as $academicYear)

                            <option
                                value="{{ $academicYear->id }}"
                                @selected(
                                    (string) $academicYearId ===
                                    (string) $academicYear->id
                                )
                            >
                                {{ $academicYear->name }}
                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- Unit --}}
                <div class="col-md-6 col-lg-3">

                    <label
                        for="organization_id"
                        class="form-label fw-semibold"
                    >
                        Unit
                    </label>

                    <select
                        id="organization_id"
                        name="organization_id"
                        class="form-select"
                    >

                        <option
                            value="all"
                            @selected($organizationId === '' || $organizationId === 'all')
                        >
                            Semua Unit
                        </option>

                        @foreach ($organizations as $organization)

                            <option
                                value="{{ $organization->id }}"
                                @selected(
                                    (string) $organizationId ===
                                    (string) $organization->id
                                )
                            >
                                {{ $organization->name }}
                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- Jenis Tagihan --}}
                <div class="col-md-6 col-lg-2">

                    <label
                        for="bill_type_id"
                        class="form-label fw-semibold"
                    >
                        Jenis Tagihan
                    </label>

                    <select
                        id="bill_type_id"
                        name="bill_type_id"
                        class="form-select"
                    >

                        <option
                            value="all"
                            @selected($billTypeId === '' || $billTypeId === 'all')
                        >
                            Semua Jenis Tagihan
                        </option>

                        @foreach ($billTypes as $billType)

                            <option
                                value="{{ $billType->id }}"
                                @selected(
                                    (string) $billTypeId ===
                                    (string) $billType->id
                                )
                            >
                                {{ $billType->name }}
                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- Periode --}}
                <div class="col-md-6 col-lg-3">

                    <label
                        for="period"
                        class="form-label fw-semibold"
                    >
                        Periode
                    </label>

                    <select
                        id="period"
                        name="period"
                        class="form-select"
                    >

                        <option
                            value="all"
                            @selected($period === '' || $period === 'all')
                        >
                            Semua Periode
                        </option>

                        @foreach ($periods as $item)

                            <option
                                value="{{ $item }}"
                                @selected($period === $item)
                            >
                                {{ $item }}
                            </option>

                        @endforeach

                    </select>

                </div>


                {{-- Status --}}
                <div class="col-md-6 col-lg-2">

                    <label
                        for="status"
                        class="form-label fw-semibold"
                    >
                        Status Tagihan
                    </label>

                    <select
                        id="status"
                        name="status"
                        class="form-select"
                    >

                        <option
                            value="all"
                            @selected($status === '' || $status === 'all')
                        >
                            Semua Status
                        </option>

                        <option
                            value="active"
                            @selected($status === 'active')
                        >
                            Aktif
                        </option>

                        <option
                            value="cancelled"
                            @selected($status === 'cancelled')
                        >
                            Dibatalkan
                        </option>

                    </select>

                </div>

            </div>


            <hr class="my-4">


            {{-- Action Buttons --}}
            <div class="d-flex flex-wrap align-items-center gap-2">

                {{-- Tampilkan --}}
                <button
                    type="submit"
                    class="btn btn-primary px-4"
                >
                    <i class="bi bi-search me-1"></i>
                    Tampilkan
                </button>


                {{-- Reset --}}
                <a
                    href="{{ route('admin.reports.bills.index') }}"
                    class="btn btn-outline-secondary px-4"
                >
                    <i class="bi bi-arrow-counterclockwise me-1"></i>
                    Reset
                </a>


                <div class="vr mx-2 d-none d-md-block"></div>


                {{-- Excel --}}
                <a
                    href="{{ route('admin.reports.bills.excel', request()->query()) }}"
                    class="btn btn-outline-success px-4"
                >
                    <i class="bi bi-file-earmark-excel me-1"></i>
                    Excel
                </a>


                {{-- PDF --}}
                <a
                    href="{{ route('admin.reports.bills.pdf', request()->query()) }}"
                    class="btn btn-outline-danger px-4"
                >
                    <i class="bi bi-file-earmark-pdf me-1"></i>
                    PDF
                </a>

            </div>

        </form>

    </div>

</div>


@push('js')

<script>

document.addEventListener('DOMContentLoaded', function () {

    const academicYearSelect =
        document.getElementById('academic_year_id');

    const organizationSelect =
        document.getElementById('organization_id');

    const billTypeSelect =
        document.getElementById('bill_type_id');

    const periodSelect =
        document.getElementById('period');


    /*
    |--------------------------------------------------------------------------
    | Nilai pilihan dari request
    |--------------------------------------------------------------------------
    */

    const selectedBillTypeId =
        @json($billTypeId);

    const selectedPeriod =
        @json($period);


    /*
    |--------------------------------------------------------------------------
    | Load Jenis Tagihan
    |--------------------------------------------------------------------------
    */

    async function loadBillTypes(keepSelection = true) {

        const academicYearId =
            academicYearSelect.value;

        const organizationId =
            organizationSelect.value;


        billTypeSelect.innerHTML = '';


        const allOption =
            document.createElement('option');

        allOption.value = 'all';

        allOption.textContent =
            'Semua Jenis Tagihan';

        allOption.selected = true;

        billTypeSelect.appendChild(allOption);


        const params =
            new URLSearchParams();


        /*
        | Kirim nilai "all" juga.
        | Controller akan menormalisasinya.
        */

        params.append(
            'academic_year_id',
            academicYearId
        );

        params.append(
            'organization_id',
            organizationId
        );


        try {

            billTypeSelect.disabled = true;


            const response = await fetch(
                `{{ route('admin.reports.bills.bill-types') }}?${params.toString()}`,
                {
                    headers: {
                        'Accept': 'application/json'
                    }
                }
            );


            if (!response.ok) {

                throw new Error(
                    'Gagal mengambil jenis tagihan.'
                );

            }


            const billTypes =
                await response.json();


            billTypes.forEach(function (billType) {

                const option =
                    document.createElement('option');

                option.value =
                    billType.id;

                option.textContent =
                    billType.name;


                if (
                    keepSelection &&
                    selectedBillTypeId &&
                    selectedBillTypeId !== 'all' &&
                    Number(selectedBillTypeId) ===
                    Number(billType.id)
                ) {

                    option.selected = true;

                }


                billTypeSelect.appendChild(option);

            });


        } catch (error) {

            console.error(error);

        } finally {

            billTypeSelect.disabled = false;

        }

    }


    /*
    |--------------------------------------------------------------------------
    | Load Periode
    |--------------------------------------------------------------------------
    */

    async function loadPeriods(keepSelection = true) {

        const academicYearId =
            academicYearSelect.value;

        const organizationId =
            organizationSelect.value;

        const billTypeId =
            billTypeSelect.value;


        periodSelect.innerHTML = '';


        const allOption =
            document.createElement('option');

        allOption.value = 'all';

        allOption.textContent =
            'Semua Periode';

        allOption.selected = true;

        periodSelect.appendChild(allOption);


        const params =
            new URLSearchParams();


        /*
        | Kirim nilai "all" juga.
        */

        params.append(
            'academic_year_id',
            academicYearId
        );

        params.append(
            'organization_id',
            organizationId
        );

        params.append(
            'bill_type_id',
            billTypeId
        );


        try {

            periodSelect.disabled = true;


            const response = await fetch(
                `{{ route('admin.reports.bills.periods') }}?${params.toString()}`,
                {
                    headers: {
                        'Accept': 'application/json'
                    }
                }
            );


            if (!response.ok) {

                throw new Error(
                    'Gagal mengambil periode.'
                );

            }


            const periods =
                await response.json();


            periods.forEach(function (period) {

                const option =
                    document.createElement('option');

                option.value =
                    period;

                option.textContent =
                    period;


                if (
                    keepSelection &&
                    selectedPeriod &&
                    selectedPeriod !== 'all' &&
                    selectedPeriod === period
                ) {

                    option.selected = true;

                }


                periodSelect.appendChild(option);

            });

        } catch (error) {

            console.error(error);

        } finally {

            periodSelect.disabled = false;

        }

    }


    /*
    |--------------------------------------------------------------------------
    | Perubahan Tahun Ajaran
    |--------------------------------------------------------------------------
    */

    academicYearSelect.addEventListener(
        'change',
        async function () {

            // Reset filter turunan
            billTypeSelect.value = 'all';
            periodSelect.value = 'all';


            await loadBillTypes(false);

            await loadPeriods(false);

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Perubahan Unit
    |--------------------------------------------------------------------------
    */

    organizationSelect.addEventListener(
        'change',
        async function () {

            // Reset filter turunan
            billTypeSelect.value = 'all';
            periodSelect.value = 'all';


            await loadBillTypes(false);

            await loadPeriods(false);

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Perubahan Jenis Tagihan
    |--------------------------------------------------------------------------
    */

    billTypeSelect.addEventListener(
        'change',
        function () {

            loadPeriods(false);

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Load awal
    |--------------------------------------------------------------------------
    */

    loadBillTypes(true).then(function () {

        loadPeriods(true);

    });

});

</script>

@endpush
