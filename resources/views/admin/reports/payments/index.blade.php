@extends('adminlte::page')
@section('title', 'Laporan Pembayaran')
@section('content_header')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
        <div>
            <h1 class="mb-1">
                Laporan Pembayaran
            </h1>
            <p class="text-muted mb-0">
                Laporan pembayaran siswa berdasarkan periode, unit,
                metode pembayaran, dan status.
            </p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal"
                data-bs-target="#modalFilterPaymentReport">
                <i class="bi bi-funnel"></i>
                Filter
            </button>

            <a href="{{ route('admin.reports.payments.export-excel', request()->query()) }}" class="btn btn-success">
                <i class="bi bi-file-earmark-excel"></i>
                Excel
            </a>

            <a href="{{ route('admin.reports.payments.export-pdf', request()->query()) }}" class="btn btn-danger">
                <i class="bi bi-file-earmark-pdf"></i>
                PDF
            </a>
        </div>
    </div>
@stop


@push('css')
    <style>
        .card-footer .pagination {
            margin-bottom: 0;
        }

        .payment-student-list div+div {
            margin-top: 3px;
        }
    </style>
@endpush


@section('content')
    {{-- =====================================================
    RINGKASAN
    ====================================================== --}}
    @include('admin.reports.payments.partials.summary')

    {{-- =====================================================
    TABEL
    ====================================================== --}}
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    Daftar Pembayaran
                </h5>
                <span class="text-muted">
                    {{ $payments->total() }} transaksi
                </span>
            </div>
        </div>

        <div class="card-body">
            @if ($payments->count())
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Tanggal</th>
                                <th>Nomor Pembayaran</th>
                                <th>Unit</th>
                                <th>Siswa</th>
                                <th>Tagihan</th>
                                <th class="text-end">
                                    Nominal
                                </th>
                                <th>Metode</th>
                                <th>Status</th>
                                <th class="text-center">
                                    Aksi
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($payments as $payment)
                                <tr>
                                    <td>
                                        {{ $payments->firstItem() + $loop->index }}
                                    </td>

                                    <td class="text-nowrap">
                                        {{ $payment->payment_date?->format('d/m/Y') }}
                                        <br>
                                        <small class="text-muted">
                                            {{ $payment->payment_date?->format('H:i') }}
                                        </small>
                                    </td>

                                    <td>
                                        <strong>
                                            {{ $payment->payment_number }}
                                        </strong>
                                    </td>

                                    <td>
                                        {{ $payment->organization?->name ?? '-' }}
                                    </td>

                                    <td>
                                        <div class="payment-student-list">
                                            @forelse ($payment->allocations as $allocation)
                                                <div>
                                                    {{ $allocation->studentBill?->studentAcademicYear?->student?->name ?? '-' }}
                                                </div>
                                            @empty

                                                <span class="text-muted">
                                                    -
                                                </span>
                                            @endforelse

                                        </div>
                                    </td>

                                    <td>
                                        <div>
                                            @forelse ($payment->allocations as $allocation)
                                                <div>
                                                    {{ $allocation->studentBill?->billType?->name ?? '-' }}
                                                    @if ($allocation->studentBill?->period)
                                                        <small class="text-muted">
                                                            ({{ $allocation->studentBill->period }})
                                                        </small>
                                                    @endif

                                                </div>
                                            @empty

                                                <span class="text-muted">
                                                    -
                                                </span>
                                            @endforelse

                                        </div>
                                    </td>

                                    <td class="text-end text-nowrap">
                                        Rp
                                        {{ number_format($payment->amount, 0, ',', '.') }}
                                    </td>

                                    <td>
                                        @switch($payment->payment_method)
                                            @case('cash')
                                                Tunai
                                            @break

                                            @case('bank_transfer')
                                                Transfer Bank
                                            @break

                                            @case('online')
                                                Online
                                            @break

                                            @default
                                                {{ $payment->payment_method }}
                                        @endswitch

                                    </td>

                                    <td>
                                        @switch($payment->status)
                                            @case('pending')
                                                <span class="badge bg-warning text-dark">
                                                    Menunggu
                                                </span>
                                            @break

                                            @case('confirmed')
                                                <span class="badge bg-success">
                                                    Dikonfirmasi
                                                </span>
                                            @break

                                            @case('failed')
                                                <span class="badge bg-danger">
                                                    Gagal
                                                </span>
                                            @break

                                            @case('cancelled')
                                                <span class="badge bg-secondary">
                                                    Dibatalkan
                                                </span>
                                            @break

                                            @default
                                                <span class="badge bg-light text-dark">
                                                    {{ $payment->status }}
                                                </span>
                                        @endswitch

                                    </td>

                                    <td class="text-center">
                                        <button type="button"
                                            class="btn btn-sm btn-outline-primary btn-payment-report-detail"
                                            data-payment-id="{{ $payment->id }}" data-bs-toggle="modal"
                                            data-bs-target="#modalPaymentReportDetail">
                                            <i class="bi bi-eye"></i>
                                            Detail

                                        </button>
                                    </td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <div class="text-muted mb-3">
                        <i class="bi bi-credit-card" style="font-size: 3rem;"></i>
                    </div>
                    <h5>
                        Tidak ada data pembayaran
                    </h5>
                    <p class="text-muted mb-0">
                        Tidak ditemukan pembayaran sesuai filter yang dipilih.
                    </p>
                </div>
            @endif

        </div>

        {{-- =====================================================
        PAGINATION
        ====================================================== --}}
        @if ($payments->hasPages())
            <div class="card-footer">
                <div class="d-flex justify-content-end">
                    {{ $payments->onEachSide(1)->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif

    </div>
@stop


@include('admin.reports.payments.partials.filter')
@include('admin.reports.payments.partials.detail')
