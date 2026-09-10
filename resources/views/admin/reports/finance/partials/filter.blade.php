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
                    Tentukan periode dan kriteria laporan keuangan
                </small>
            </div>

        </div>

    </div>


    {{-- Body --}}
    <div class="card-body p-4">

        <form
            method="GET"
            action="{{ route('admin.reports.finance.index') }}"
        >

            <div class="row g-3">

                {{-- Tanggal Mulai --}}
                <div class="col-md-6 col-lg-2">

                    <label
                        for="date_from"
                        class="form-label fw-semibold"
                    >
                        Tanggal Mulai
                    </label>

                    <div class="input-group">

                        <span class="input-group-text bg-light">
                            <i class="bi bi-calendar3"></i>
                        </span>

                        <input
                            type="date"
                            id="date_from"
                            name="date_from"
                            class="form-control"
                            value="{{ $dateFrom }}"
                        >

                    </div>

                </div>


                {{-- Tanggal Akhir --}}
                <div class="col-md-6 col-lg-2">

                    <label
                        for="date_to"
                        class="form-label fw-semibold"
                    >
                        Tanggal Akhir
                    </label>

                    <div class="input-group">

                        <span class="input-group-text bg-light">
                            <i class="bi bi-calendar3"></i>
                        </span>

                        <input
                            type="date"
                            id="date_to"
                            name="date_to"
                            class="form-control"
                            value="{{ $dateTo }}"
                        >

                    </div>

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

                        <option value="all">
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


                {{-- Jenis Transaksi --}}
                <div class="col-md-6 col-lg-2">

                    <label
                        for="type"
                        class="form-label fw-semibold"
                    >
                        Jenis Transaksi
                    </label>

                    <select
                        id="type"
                        name="type"
                        class="form-select"
                    >

                        <option
                            value="all"
                            @selected($type === 'all')
                        >
                            Semua
                        </option>

                        <option
                            value="income"
                            @selected($type === 'income')
                        >
                            Pemasukan
                        </option>

                        <option
                            value="expense"
                            @selected($type === 'expense')
                        >
                            Pengeluaran
                        </option>

                    </select>

                </div>


                {{-- Kategori --}}
                <div class="col-md-6 col-lg-3">

                    <label
                        for="category"
                        class="form-label fw-semibold"
                    >
                        Kategori
                    </label>

                    <select
                        id="category"
                        name="category"
                        class="form-select"
                    >

                        <option
                            value="all"
                            @selected($category === 'all')
                        >
                            Semua Kategori
                        </option>

                        @foreach ($categories as $item)

                            <option
                                value="{{ $item }}"
                                @selected($category === $item)
                            >
                                {{ $item }}
                            </option>

                        @endforeach

                    </select>

                </div>

            </div>


            {{-- Divider --}}
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
                    href="{{ route('admin.reports.finance.index') }}"
                    class="btn btn-outline-secondary px-4"
                >
                    <i class="bi bi-arrow-counterclockwise me-1"></i>
                    Reset
                </a>


                <div class="vr mx-2 d-none d-md-block"></div>


                {{-- Excel --}}
                <a
                    href="{{ route('admin.reports.finance.excel', request()->query()) }}"
                    class="btn btn-outline-success px-4"
                >
                    <i class="bi bi-file-earmark-excel me-1"></i>
                    Excel
                </a>


                {{-- PDF --}}
                <a
                    href="{{ route('admin.reports.finance.pdf', request()->query()) }}"
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
        document.addEventListener('DOMContentLoaded', function() {

            const organizationSelect =
                document.getElementById('organization_id');

            const typeSelect =
                document.getElementById('type');

            const categorySelect =
                document.getElementById('category');


            async function loadCategories() {

                const organizationId =
                    organizationSelect.value;

                const type =
                    typeSelect.value;


                categorySelect.innerHTML = '';

                const allOption =
                    document.createElement('option');

                allOption.value = 'all';

                allOption.textContent =
                    'Semua Kategori';

                categorySelect.appendChild(allOption);


                const params =
                    new URLSearchParams();


                params.append(
                    'organization_id',
                    organizationId
                );

                params.append(
                    'type',
                    type
                );


                try {

                    categorySelect.disabled = true;


                    const response = await fetch(
                        `{{ route('admin.reports.finance.categories') }}?${params.toString()}`, {
                            headers: {
                                'Accept': 'application/json'
                            }
                        }
                    );


                    if (!response.ok) {

                        throw new Error(
                            'Gagal mengambil data kategori.'
                        );

                    }


                    const categories =
                        await response.json();


                    categories.forEach(function(category) {

                        const option =
                            document.createElement('option');

                        option.value = category;

                        option.textContent = category;

                        categorySelect.appendChild(option);

                    });


                } catch (error) {

                    console.error(error);


                } finally {

                    categorySelect.disabled = false;

                }

            }


            organizationSelect.addEventListener(
                'change',
                function() {

                    loadCategories();

                }
            );


            typeSelect.addEventListener(
                'change',
                function() {

                    loadCategories();

                }
            );

        });
    </script>
@endpush
