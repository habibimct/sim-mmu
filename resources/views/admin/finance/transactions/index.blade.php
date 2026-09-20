@extends('adminlte::page')

@section('title', 'Transaksi Keuangan')

@section('content_header')
    @if ($pendingDeposits->count())
        <div class="alert alert-warning">

            <i class="bi bi-hourglass-split me-1"></i>

            Ada
            <strong>{{ $pendingDeposits->count() }}</strong>
            setoran unit yang menunggu konfirmasi.

        </div>
    @endif
    <div class="d-flex justify-content-between align-items-center">

        <div>
            <h1>
                <i class="bi bi-cash-stack me-1"></i>
                Transaksi Keuangan
            </h1>

            <span id="connectionStatus" class="badge bg-success">
                <i class="bi bi-wifi me-1"></i> Online
            </span>
            <span id="pendingSyncStatus" class="badge bg-warning text-dark">
                <i class="bi bi-cloud-arrow-up me-1"></i>
                <span id="pendingSyncCount">0</span> menunggu sinkronisasi
            </span>
        </div>

        @can('finance.manage')
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTransaksiBaru">
                <i class="bi bi-plus-lg me-1"></i>
                Transaksi Baru
            </button>
        @endcan

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

    {{-- Filter --}}
    @include('admin.finance.transactions.partials.filter')

    {{-- Ringkasan --}}
    @include('admin.finance.transactions.partials.summary')

    {{-- Daftar transaksi --}}
    @include('admin.finance.transactions.partials.table')

    {{-- Setoran pending --}}
    @include('admin.finance.transactions.partials.pending-deposits')

    {{-- Modal transaksi baru --}}
    @include('admin.finance.transactions.partials.modal-new')

    {{-- Modal penolakan --}}
    @include('admin.finance.transactions.partials.modal-reject-deposit')

@stop


@push('js')
    @include('admin.finance.transactions.partials.scripts')
@endpush


@include('admin.finance.transactions.partials.cancel')
