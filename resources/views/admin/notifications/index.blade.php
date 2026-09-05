@extends('adminlte::page')

@section('title', 'Notifikasi')

@section('content_header')

    <h1>
        <i class="bi bi-bell me-1"></i>
        Notifikasi
    </h1>

@stop


@section('content')

    <div class="card">

        <div class="card-header">

            <h3 class="card-title">
                Semua Notifikasi
            </h3>

            <div class="card-tools">

                <form method="POST" action="{{ route('admin.notifications.read-all') }}">

                    @csrf

                    <button type="submit" class="btn btn-sm btn-outline-primary">

                        <i class="bi bi-check2-all me-1"></i>

                        Tandai Semua Dibaca

                    </button>

                </form>

            </div>

        </div>


        <div class="card-body p-0">

            @forelse ($notifications as $notification)
                @php
                    $data = $notification->data;
                @endphp


                <div
                    class="d-flex align-items-start gap-3 p-3 border-bottom
                    {{ $notification->read_at ? '' : 'bg-light' }}">

                    <div>

                        @if (($data['type'] ?? null) === 'finance_transaction_cancelled')
                            <i class="bi bi-x-circle-fill text-danger fs-4"></i>
                        @elseif (($data['type'] ?? null) === 'finance_deposit_rejected')
                            <i class="bi bi-x-circle-fill text-danger fs-4"></i>
                        @elseif (($data['type'] ?? null) === 'finance_deposit_confirmed')
                            <i class="bi bi-check-circle-fill text-success fs-4"></i>
                        @else
                            <i class="bi bi-cash-stack text-primary fs-4"></i>
                        @endif

                    </div>


                    <div class="flex-grow-1">

                        <div class="fw-bold">

                            {{ $data['title'] ?? 'Notifikasi' }}

                        </div>


                        <div class="text-muted">

                            {{ $data['message'] ?? '' }}

                        </div>


                        {{-- Identitas transaksi --}}
                        @if (!empty($data['transaction_id']))
                            <div class="mt-1">

                                <span class="badge text-bg-secondary">
                                    <i class="bi bi-receipt me-1"></i>
                                    Transaksi #{{ $data['transaction_id'] }}
                                </span>

                            </div>
                        @endif


                        {{-- Identitas setoran --}}
                        @if (!empty($data['deposit_id']))
                            <div class="mt-1">

                                <span class="badge text-bg-secondary">
                                    <i class="bi bi-bank me-1"></i>
                                    Setoran #{{ $data['deposit_id'] }}
                                </span>

                            </div>
                        @endif


                        {{-- Identitas pembayaran --}}
                        @if (!empty($data['payment_id']))
                            <div class="mt-1">

                                <span class="badge text-bg-secondary">
                                    <i class="bi bi-credit-card me-1"></i>
                                    Pembayaran #{{ $data['payment_id'] }}
                                </span>

                            </div>
                        @endif


                        @if (!empty($data['rejection_reason']))
                            <div class="mt-2">

                                <strong>
                                    Alasan penolakan:
                                </strong>

                                {{ $data['rejection_reason'] }}

                            </div>
                        @endif


                        <small class="text-muted">

                            {{ $notification->created_at->format('d/m/Y H:i') }}

                        </small>

                    </div>


                    @if (!$notification->read_at)
                        <form method="POST" action="{{ route('admin.notifications.read', $notification) }}">

                            @csrf

                            <button type="submit" class="btn btn-sm btn-outline-secondary">

                                <i class="bi bi-check2"></i>

                                Dibaca

                            </button>

                        </form>
                    @endif

                </div>

            @empty

                <div class="text-center text-muted p-5">

                    <i class="bi bi-bell-slash fs-2"></i>

                    <div class="mt-2">
                        Tidak ada notifikasi.
                    </div>

                </div>
            @endforelse

        </div>


        @if ($notifications->hasPages())
            <div class="card-footer">

                {{ $notifications->links() }}

            </div>
        @endif

    </div>

@stop
