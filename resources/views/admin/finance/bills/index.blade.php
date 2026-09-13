@extends('adminlte::page')

@section('title', 'Tagihan Siswa')

@section('content_header')

    <div class="d-flex justify-content-between align-items-center">

        <div>
            <h1>Tagihan Siswa</h1>

            <p class="text-muted mb-0">
                Kelola tagihan siswa berdasarkan unit.
            </p>
        </div>
        <div>
            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal"
                data-bs-target="#modalFilterStudentBills">
                <i class="bi bi-funnel me-1"></i>
                Filter
            </button>

@can('bills.manage')
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCreateStudentBill">
        <i class="bi bi-plus-lg me-1"></i>
        Tambah Tagihan
    </button>
@endcan
        </div>
    </div>

@stop

@push('css')
    <style>
        .card-footer .pagination {
            margin-bottom: 0;
        }
    </style>
@endpush


@section('content')

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

    {{-- =====================================================
    RINGKASAN
    ====================================================== --}}

    @include('admin.finance.bills.partials.summary')

    {{-- =========================================================
    TABEL
    ========================================================== --}}

    <div class="card">

        <div class="card-header">

            <h3 class="card-title">
                Daftar Tagihan Siswa
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
                                Siswa
                            </th>

                            <th>
                                Unit
                            </th>

                            <th>
                                Jenis Tagihan
                            </th>

                            <th>
                                Periode
                            </th>

                            <th>
                                Nominal
                            </th>

                            <th>
                                Jatuh Tempo
                            </th>

                            <th>
                                Status
                            </th>

                            <th class="text-end" style="width: 150px;">
                                Aksi
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse ($studentBills as $studentBill)
                            <tr>

                                <td>
                                    {{ $studentBills->firstItem() + $loop->index }}
                                </td>


                                {{-- Siswa --}}

                                <td>

                                    <div class="fw-semibold">

                                        {{ $studentBill->studentAcademicYear->student->name }}

                                    </div>

                                </td>


                                {{-- Unit --}}

                                <td>

                                    {{ $studentBill->billType->organization->name }}

                                </td>


                                {{-- Jenis tagihan --}}

                                <td>

                                    <span class="fw-medium">

                                        {{ $studentBill->billType->name }}

                                    </span>

                                    <div class="text-muted small">

                                        {{ $studentBill->billType->code }}

                                    </div>

                                </td>


                                {{-- Periode --}}

                                <td>

                                    {{ $studentBill->period }}

                                </td>


                                {{-- Nominal --}}

                                <td>

                                    Rp
                                    {{ number_format($studentBill->amount, 0, ',', '.') }}

                                </td>


                                {{-- Jatuh tempo --}}

                                <td>

                                    @if ($studentBill->due_date)
                                        {{ $studentBill->due_date->format('d/m/Y') }}
                                    @else
                                        <span class="text-muted">
                                            —
                                        </span>
                                    @endif

                                </td>


                                {{-- Status --}}

                                <td>

                                    @switch($studentBill->status)
                                        @case('unpaid')
                                            <span class="badge text-bg-warning">
                                                Belum Bayar
                                            </span>
                                        @break

                                        @case('partial')
                                            <span class="badge text-bg-info">
                                                Sebagian
                                            </span>
                                        @break

                                        @case('paid')
                                            <span class="badge text-bg-success">
                                                Lunas
                                            </span>
                                        @break

                                        @case('cancelled')
                                            <span class="badge text-bg-secondary">
                                                Dibatalkan
                                            </span>
                                        @break

                                        @default
                                            <span class="badge text-bg-light">
                                                {{ $studentBill->status }}
                                            </span>
                                    @endswitch

                                </td>


                                {{-- Aksi --}}

                                <td class="text-end">

                                    @can('view', $studentBill)
                                        <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal"
                                            data-bs-target="#modalShowStudentBill"
                                            data-student="{{ $studentBill->studentAcademicYear->student->name }}"
                                            data-nis="{{ $studentBill->studentAcademicYear->student->nis }}"
                                            data-organization="{{ $studentBill->studentAcademicYear->organization->name }}"
                                            data-academic-year="{{ $studentBill->studentAcademicYear->academicYear->name }}"
                                            data-school-class="{{ $studentBill->studentAcademicYear->schoolClass?->name ?? '—' }}"
                                            data-bill-type="{{ $studentBill->billType->name }}"
                                            data-period="{{ $studentBill->period }}"
                                            data-due-date="{{ $studentBill->due_date?->format('d/m/Y') ?? '—' }}"
                                            data-amount="Rp {{ number_format($studentBill->amount, 0, ',', '.') }}"
                                            data-status="{{ $studentBill->status }}"
                                            data-description="{{ $studentBill->description ?? '' }}"
                                            data-cancellation-reason="{{ $studentBill->cancellation_reason ?? '' }}"
                                            data-cancelled-by="{{ $studentBill->canceller?->name ?? '' }}"
                                            data-cancelled-at="{{ $studentBill->cancelled_at?->format('d/m/Y H:i') ?? '' }}">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    @endcan


                                    @can('update', $studentBill)
                                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                            data-bs-target="#modalEditStudentBill"
                                            data-update-url="{{ route('admin.finance.bills.update', $studentBill) }}"
                                            data-student="{{ $studentBill->studentAcademicYear->student->name }}"
                                            data-organization="{{ $studentBill->billType->organization->name }}"
                                            data-academic-year="{{ $studentBill->studentAcademicYear->academicYear->name }}"
                                            data-bill-type-id="{{ $studentBill->bill_type_id }}"
                                            data-period="{{ $studentBill->period }}" data-amount="{{ $studentBill->amount }}"
                                            data-due-date="{{ $studentBill->due_date?->format('Y-m-d') }}"
                                            data-description="{{ $studentBill->description }}">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                    @endcan

                                    @can('cancel', $studentBill)
                                        @if ($studentBill->status === 'unpaid')
                                            <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                                                data-bs-target="#modalCancelStudentBill"
                                                data-cancel-url="{{ route('admin.finance.bills.cancel', $studentBill) }}"
                                                data-student="{{ $studentBill->studentAcademicYear->student->name }}"
                                                data-bill-type="{{ $studentBill->billType->name }}"
                                                data-period="{{ $studentBill->period }}"
                                                data-amount="Rp {{ number_format($studentBill->amount, 0, ',', '.') }}">
                                                <i class="bi bi-x-circle"></i>
                                            </button>
                                        @else
                                            <span class="text-muted small"
                                                title="Tagihan sudah memiliki pembayaran atau sudah tidak aktif.">
                                                Tidak dapat dibatalkan
                                            </span>
                                        @endif
                                    @endcan

                                </td>

                            </tr>

                            @empty

                                <tr>

                                    <td colspan="9" class="text-center py-5">

                                        <div class="text-muted">

                                            <i class="bi bi-receipt fs-2 d-block mb-2"></i>

                                            Belum ada tagihan siswa.

                                        </div>

                                    </td>

                                </tr>
                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>


            {{-- =====================================================
            PAGINATION
            ====================================================== --}}
            @if ($studentBills->hasPages())
                <div class="card-footer">
                    <div class="d-flex justify-content-end">
                        {{ $studentBills->onEachSide(1)->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            @endif

        </div>

    @stop


    @include('admin.finance.bills.partials.create')
    @include('admin.finance.bills.partials.edit')
    @include('admin.finance.bills.partials.show')
    @include('admin.finance.bills.partials.cancel')
    @include('admin.finance.bills.partials.filter')
