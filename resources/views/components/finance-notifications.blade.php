@php

    $user = auth()->user();

    $unreadFinanceNotifications = $user
        ->unreadNotifications()
        ->whereIn('data->type', [
            'finance_transaction_created',
            'finance_transaction_cancelled',
            'finance_deposit_confirmed',
            'finance_deposit_rejected',
        ])
        ->latest()
        ->take(10)
        ->get();

    $unreadFinanceCount = $user
        ->unreadNotifications()
        ->whereIn('data->type', [
            'finance_transaction_created',
            'finance_transaction_cancelled',
            'finance_deposit_confirmed',
            'finance_deposit_rejected',
        ])
        ->count();

@endphp


<li class="nav-item dropdown">

    <a class="nav-link" href="#" data-bs-toggle="dropdown" aria-expanded="false">

        <i class="bi bi-bell"></i>

        @if ($unreadFinanceCount > 0)
            <span class="navbar-badge badge text-bg-danger">

                {{ $unreadFinanceCount > 99 ? '99+' : $unreadFinanceCount }}

            </span>
        @endif

    </a>


    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end">

        <span class="dropdown-item dropdown-header">

            {{ $unreadFinanceCount }}
            Notifikasi Keuangan

        </span>


        <div class="dropdown-divider"></div>


        @forelse ($unreadFinanceNotifications as $notification)
            @php
                $data = $notification->data;
            @endphp


            <a href="{{ route('admin.notifications.index') }}" class="dropdown-item">

                <div class="d-flex align-items-start">

                    <div class="me-2">

                        @if (($data['type'] ?? null) === 'finance_transaction_cancelled')
                            <i class="bi bi-x-circle text-danger"></i>
                        @elseif (($data['type'] ?? null) === 'finance_deposit_rejected')
                            <i class="bi bi-x-circle text-danger"></i>
                        @elseif (($data['type'] ?? null) === 'finance_deposit_confirmed')
                            <i class="bi bi-check-circle text-success"></i>
                        @else
                            <i class="bi bi-cash-stack text-primary"></i>
                        @endif

                    </div>


                    <div>

                        <strong class="d-block">

                            {{ $data['title'] ?? 'Notifikasi Keuangan' }}

                        </strong>

                        <small class="text-muted">

                            {{ $data['message'] ?? '' }}

                        </small>

                    </div>

                </div>

            </a>


            <div class="dropdown-divider"></div>

        @empty

            <span class="dropdown-item text-center text-muted py-3">

                Tidak ada notifikasi baru.

            </span>
        @endforelse


        <a href="{{ route('admin.notifications.index') }}" class="dropdown-item dropdown-footer">

            Lihat semua notifikasi

        </a>

    </div>

</li>
