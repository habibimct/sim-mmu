@extends('adminlte::page')

@section('title', 'Pembayaran Siswa')

@section('content_header')

    <div class="d-flex justify-content-between align-items-center">

        <div>

            <h1 class="mb-1">
                Pembayaran Siswa
            </h1>

            <p class="text-muted mb-0">
                Daftar pembayaran tagihan siswa.
            </p>

        </div>

        <div>

            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalFilterPayments">
                <i class="bi bi-funnel me-1"></i>
                Filter
            </button>

            @can('create', App\Models\Payment::class)
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCreatePayment">
                    <i class="bi bi-plus-lg me-1"></i>
                    Tambah Pembayaran
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

    {{-- =====================================================
    RINGKASAN
    ====================================================== --}}

    @include('admin.finance.payments.partials.summary')


    {{-- =========================================================
    DAFTAR PEMBAYARAN
    ========================================================== --}}

    <div class="card">

        <div class="card-header">

            <h5 class="mb-0">
                Daftar Pembayaran
            </h5>

        </div>


        <div class="card-body">

            @if ($payments->count())

                <div class="table-responsive">

                    <table class="table table-hover align-middle">

                        <thead>

                            <tr>

                                <th>
                                    No.
                                </th>

                                <th>
                                    Nomor Pembayaran
                                </th>

                                <th>
                                    Tanggal
                                </th>

                                <th>
                                    Siswa
                                </th>

                                <th>
                                    Nominal
                                </th>

                                <th>
                                    Metode
                                </th>

                                <th>
                                    Status
                                </th>

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

                                    <td>

                                        <strong>
                                            {{ $payment->payment_number }}
                                        </strong>

                                    </td>

                                    <td>

                                        {{ $payment->payment_date?->format('d/m/Y H:i') }}

                                    </td>

                                    <td>
                                        @forelse ($payment->allocations as $allocation)
                                            <div>
                                                {{ $allocation->studentBill?->studentAcademicYear?->student?->name ?? '-' }}
                                            </div>

                                        @empty

                                            <span class="text-muted">
                                                -
                                            </span>
                                        @endforelse
                                    </td>

                                    <td>

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

                                        <button type="button" class="btn btn-sm btn-outline-primary btn-payment-detail"
                                            data-payment-id="{{ $payment->id }}" data-bs-toggle="modal"
                                            data-bs-target="#modalPaymentDetail">
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

                        <i class="bi bi-wallet2" style="font-size: 3rem;"></i>

                    </div>

                    <h5>
                        Belum ada pembayaran
                    </h5>

                    <p class="text-muted mb-0">
                        Data pembayaran siswa akan muncul di sini.
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



@include('admin.finance.payments.partials.filter')
@include('admin.finance.payments.partials.create')
@include('admin.finance.payments.partials.detail')
