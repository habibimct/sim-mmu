@php

    $user = auth()->user();

    /*
    |--------------------------------------------------------------------------
    | Notifikasi user
    |--------------------------------------------------------------------------
    |
    | Ambil langsung dari Laravel Database Notifications.
    | Semua jenis notifikasi PMUB akan masuk di sini.
    |
    */

    $notifications = $user ? $user->notifications()->latest()->limit(5)->get() : collect();

    /*
    |--------------------------------------------------------------------------
    | Jumlah notifikasi belum dibaca
    |--------------------------------------------------------------------------
    */

    $notificationCount = $user ? $user->unreadNotifications()->count() : 0;

    /*
    |--------------------------------------------------------------------------
    | Halaman semua notifikasi
    |--------------------------------------------------------------------------
    */

    $notificationsUrl = \Illuminate\Support\Facades\Route::has('admin.notifications.index')
        ? route('admin.notifications.index')
        : '#';

@endphp


<li class="nav-item dropdown">

    <a class="nav-link" data-bs-toggle="dropdown" href="#" aria-label="Notifikasi">

        <i class="bi bi-bell-fill" aria-hidden="true"></i>


        @if ($notificationCount > 0)
            <span class="navbar-badge badge text-bg-warning">
                {{ $notificationCount > 99 ? '99+' : $notificationCount }}
            </span>
        @endif

    </a>


    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end">


        {{-- =========================================================
        HEADER
        ========================================================== --}}

        <span class="dropdown-item dropdown-header">

            {{ $notificationCount }}
            {{ __('adminlte.notifications') }}

        </span>


        <div class="dropdown-divider"></div>


        {{-- =========================================================
        DAFTAR NOTIFIKASI
        ========================================================== --}}

        @forelse ($notifications as $notification)
            @php

                $data = $notification->data;

                $type = $data['type'] ?? null;

                $title = $data['title'] ?? 'Notifikasi';

                $message = $data['message'] ?? '';

                /*
                |------------------------------------------------------
                | Ikon berdasarkan jenis notifikasi
                |------------------------------------------------------
                */

                $icon = match ($type) {
                    'finance_deposit_created' => 'bi bi-arrow-down-circle text-warning',

                    'finance_deposit_confirmed' => 'bi bi-check-circle text-success',

                    'finance_deposit_rejected' => 'bi bi-x-circle text-danger',

                    'finance_transaction_created' => 'bi bi-cash-stack text-primary',

                    'finance_transaction_cancelled' => 'bi bi-x-circle text-danger',

                    'payment_pending' => 'bi bi-hourglass-split text-warning',

                    'student_payment_confirmed' => 'bi bi-credit-card text-success',

                    'student_payment_cancelled' => 'bi bi-x-circle text-danger',

                    default => 'bi bi-bell text-secondary',
                };

                /*
                |------------------------------------------------------
                | Tujuan notifikasi
                |------------------------------------------------------
                */

                $url = $data['url'] ?? $notificationsUrl;

            @endphp


            <a href="{{ $url }}" class="dropdown-item {{ $notification->read_at ? '' : 'fw-semibold' }}">

                <div class="d-flex align-items-start">

                    <i class="{{ $icon }} me-2 mt-1"></i>


                    <div class="flex-grow-1">

                        <div class="text-dark">
                            {{ $title }}
                        </div>


                        <div class="text-secondary small text-wrap">

                            {{ $message }}

                        </div>


                        <div class="text-secondary small mt-1">

                            {{ $notification->created_at->diffForHumans() }}

                        </div>

                    </div>


                    @if (!$notification->read_at)
                        <span class="badge rounded-pill text-bg-warning ms-2">
                            Baru
                        </span>
                    @endif

                </div>

            </a>


            <div class="dropdown-divider"></div>


        @empty

            <span class="dropdown-item text-secondary">

                {{ __('adminlte.no_notifications') }}

            </span>


            <div class="dropdown-divider"></div>
        @endforelse


        {{-- =========================================================
        FOOTER
        ========================================================== --}}

        <a href="{{ $notificationsUrl }}" class="dropdown-item dropdown-footer">

            {{ __('adminlte.see_all_notifications') }}

        </a>

    </div>

</li>
