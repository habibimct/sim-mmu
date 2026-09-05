@extends('adminlte::page')

@section('title', 'Jenis Tagihan')

@section('content')

    <div class="container-fluid">

        {{-- =========================================================
    HEADER
    ========================================================== --}}

        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>
                <h1 class="h4 mb-1">
                    Jenis Tagihan
                </h1>

                <p class="text-muted mb-0">
                    Kelola jenis tagihan berdasarkan organisasi/unit.
                </p>
            </div>

            @can('create', App\Models\BillType::class)
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCreateBillType">
                    <i class="bi bi-plus-lg me-1"></i>
                    Tambah Jenis Tagihan
                </button>
            @endcan

        </div>


        {{-- =========================================================
    PESAN SUKSES
    ========================================================== --}}

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">

                {{ session('success') }}

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>

            </div>
        @endif


        {{-- =========================================================
    ERROR
    ========================================================== --}}

        @if ($errors->any())

            <div class="alert alert-danger alert-dismissible fade show">

                <strong>
                    Terjadi kesalahan:
                </strong>

                <ul class="mb-0 mt-2">

                    @foreach ($errors->all() as $error)
                        <li>
                            {{ $error }}
                        </li>
                    @endforeach

                </ul>

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>

            </div>

        @endif

        {{-- =========================================================
FILTER
========================================================= --}}

        <div class="card shadow-sm mb-4">

            <div class="card-body">

                <form method="GET" action="{{ route('admin.finance.bill-types.index') }}">

                    <div class="row g-3 align-items-end">

                        {{-- Organisasi --}}

                        <div class="col-md-5">

                            <label for="filter_organization_id" class="form-label">
                                Organisasi / Unit
                            </label>

                            <select name="organization_id" id="filter_organization_id" class="form-select">

                                <option value="">
                                    Semua Organisasi
                                </option>

                                @foreach ($organizations as $organization)
                                    <option value="{{ $organization->id }}" @selected($organizationId == $organization->id)>
                                        {{ $organization->name }}
                                    </option>
                                @endforeach

                            </select>

                        </div>


                        {{-- Status --}}

                        <div class="col-md-4">

                            <label for="filter_status" class="form-label">
                                Status
                            </label>

                            <select name="status" id="filter_status" class="form-select">

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


                        {{-- Tombol --}}

                        <div class="col-md-3">

                            <div class="d-flex gap-2">

                                <a href="{{ route('admin.finance.bill-types.index') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-counterclockwise me-1"></i>
                                    Reset
                                </a>

                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-funnel me-1"></i>
                                    Terapkan
                                </button>

                            </div>

                        </div>

                    </div>

                </form>

            </div>

        </div>

        {{-- =========================================================
    TABEL JENIS TAGIHAN
    ========================================================== --}}

        <div class="card shadow-sm">

            <div class="card-header">

                <h3 class="card-title mb-0">
                    Daftar Jenis Tagihan
                </h3>

            </div>


            <div class="card-body p-0">

                <div class="table-responsive">

                    <table class="table table-hover align-middle mb-0">

                        <thead>

                            <tr>

                                <th style="width: 60px;">
                                    #
                                </th>

                                <th>
                                    Organisasi
                                </th>

                                <th>
                                    Kode
                                </th>

                                <th>
                                    Nama
                                </th>

                                <th>
                                    Deskripsi
                                </th>

                                <th>
                                    Status
                                </th>

                                <th class="text-end" style="width: 180px;">
                                    Aksi
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            @forelse ($billTypes as $billType)
                                <tr>

                                    <td>
                                        {{ $loop->iteration }}
                                    </td>

                                    <td>
                                        <span class="fw-medium">
                                            {{ $billType->organization->name }}
                                        </span>
                                    </td>

                                    <td>

                                        <span class="badge text-bg-light border">
                                            {{ $billType->code }}
                                        </span>

                                    </td>

                                    <td>
                                        {{ $billType->name }}
                                    </td>

                                    <td>

                                        @if ($billType->description)
                                            <span class="text-muted">
                                                {{ $billType->description }}
                                            </span>
                                        @else
                                            <span class="text-muted">
                                                —
                                            </span>
                                        @endif

                                    </td>

                                    <td>

                                        @if ($billType->is_active)
                                            <span class="badge text-bg-success">
                                                Aktif
                                            </span>
                                        @else
                                            <span class="badge text-bg-secondary">
                                                Tidak Aktif
                                            </span>
                                        @endif

                                    </td>

                                    <td class="text-end">

                                        @can('view', $billType)
                                            <button type="button" class="btn btn-sm btn-outline-secondary"
                                                data-bs-toggle="modal" data-bs-target="#modalShowBillType"
                                                data-organization="{{ $billType->organization->name }}"
                                                data-code="{{ $billType->code }}" data-name="{{ $billType->name }}"
                                                data-description="{{ $billType->description }}"
                                                data-status="{{ $billType->is_active ? 'Aktif' : 'Tidak Aktif' }}">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        @endcan


                                        @can('update', $billType)
                                            @can('update', $billType)
                                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                                    data-bs-target="#modalEditBillType" data-id="{{ $billType->id }}"
                                                    data-organization-id="{{ $billType->organization_id }}"
                                                    data-code="{{ $billType->code }}" data-name="{{ $billType->name }}"
                                                    data-description="{{ $billType->description }}"
                                                    data-is-active="{{ $billType->is_active ? '1' : '0' }}">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                            @endcan
                                        @endcan


                                        @can('delete', $billType)
                                            @can('delete', $billType)
                                                <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                                                    data-bs-target="#modalDeleteBillType" data-id="{{ $billType->id }}"
                                                    data-organization="{{ $billType->organization->name }}"
                                                    data-code="{{ $billType->code }}" data-name="{{ $billType->name }}">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            @endcan
                                        @endcan

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="7" class="text-center py-5">

                                        <div class="text-muted">

                                            <i class="bi bi-receipt fs-2 d-block mb-2"></i>

                                            Belum ada jenis tagihan.

                                        </div>

                                    </td>

                                </tr>
                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

@endsection

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const modal =
                document.getElementById('modalShowBillType');

            if (!modal) {
                return;
            }

            modal.addEventListener(
                'show.bs.modal',
                function(event) {

                    const button =
                        event.relatedTarget;

                    document.getElementById(
                            'showBillTypeOrganization'
                        ).textContent =
                        button.getAttribute(
                            'data-organization'
                        ) || '—';

                    document.getElementById(
                            'showBillTypeCode'
                        ).textContent =
                        button.getAttribute(
                            'data-code'
                        ) || '—';

                    document.getElementById(
                            'showBillTypeName'
                        ).textContent =
                        button.getAttribute(
                            'data-name'
                        ) || '—';

                    document.getElementById(
                            'showBillTypeDescription'
                        ).textContent =
                        button.getAttribute(
                            'data-description'
                        ) || '—';

                    const status =
                        button.getAttribute(
                            'data-status'
                        );

                    document.getElementById(
                            'showBillTypeStatus'
                        ).innerHTML =
                        status === 'Aktif' ?
                        '<span class="badge text-bg-success">Aktif</span>' :
                        '<span class="badge text-bg-secondary">Tidak Aktif</span>';
                }
            );

        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const modal =
                document.getElementById('modalEditBillType');

            const form =
                document.getElementById('formEditBillType');

            if (!modal || !form) {
                return;
            }


            modal.addEventListener(
                'show.bs.modal',
                function(event) {

                    const button =
                        event.relatedTarget;

                    const id =
                        button.getAttribute('data-id');

                    const organizationId =
                        button.getAttribute(
                            'data-organization-id'
                        );

                    const code =
                        button.getAttribute('data-code');

                    const name =
                        button.getAttribute('data-name');

                    const description =
                        button.getAttribute(
                            'data-description'
                        );

                    const isActive =
                        button.getAttribute(
                            'data-is-active'
                        );


                    /*
                    |--------------------------------------------------------------------------
                    | Action form
                    |--------------------------------------------------------------------------
                    */

                    form.action =
                        '{{ url('/admin/finance/bill-types') }}' +
                        '/' +
                        id;


                    /*
                    |--------------------------------------------------------------------------
                    | Isi form
                    |--------------------------------------------------------------------------
                    */

                    document.getElementById(
                            'edit_organization_id'
                        ).value =
                        organizationId || '';


                    document.getElementById(
                            'edit_code'
                        ).value =
                        code || '';


                    document.getElementById(
                            'edit_name'
                        ).value =
                        name || '';


                    document.getElementById(
                            'edit_description'
                        ).value =
                        description || '';


                    document.getElementById(
                            'edit_is_active'
                        ).checked =
                        isActive === '1';

                }
            );

        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const modal =
                document.getElementById('modalDeleteBillType');

            const form =
                document.getElementById('formDeleteBillType');

            if (!modal || !form) {
                return;
            }


            modal.addEventListener(
                'show.bs.modal',
                function(event) {

                    const button =
                        event.relatedTarget;


                    const id =
                        button.getAttribute('data-id');

                    const organization =
                        button.getAttribute(
                            'data-organization'
                        );

                    const code =
                        button.getAttribute('data-code');

                    const name =
                        button.getAttribute('data-name');


                    /*
                    |--------------------------------------------------------------------------
                    | Action form
                    |--------------------------------------------------------------------------
                    */

                    form.action =
                        '{{ url('/admin/finance/bill-types') }}' +
                        '/' +
                        id;


                    /*
                    |--------------------------------------------------------------------------
                    | Informasi jenis tagihan
                    |--------------------------------------------------------------------------
                    */

                    document.getElementById(
                            'deleteBillTypeOrganization'
                        ).textContent =
                        organization || '—';


                    document.getElementById(
                            'deleteBillTypeCode'
                        ).textContent =
                        code || '—';


                    document.getElementById(
                            'deleteBillTypeName'
                        ).textContent =
                        name || '—';

                }
            );

        });
    </script>
@endpush

@include('admin.finance.bill-types.partials.create')
@include('admin.finance.bill-types.partials.show')
@include('admin.finance.bill-types.partials.edit')
@include('admin.finance.bill-types.partials.delete')
