@extends('layouts.kepala-unit')


@section('content')
    <div class="flex items-center justify-between">

        <div>
            <h2 class="text-xl font-semibold text-gray-800">
                Notifikasi
            </h2>

            <p class="mt-1 text-sm text-gray-500">
                Informasi transaksi dan aktivitas keuangan Anda.
            </p>
        </div>

        @if ($notifications->total() > 0)
            <form method="POST" action="{{ route('notifications.read-all') }}">
                @csrf

                <button type="submit"
                    class="inline-flex items-center rounded-md
                               bg-indigo-600 px-4 py-2 text-sm
                               font-medium text-white
                               hover:bg-indigo-700">
                    Tandai Semua Dibaca
                </button>

            </form>
        @endif

    </div>



    <div class="py-6">

        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">

            @if (session('success'))
                <div
                    class="mb-5 rounded-lg border border-green-200
                           bg-green-50 px-4 py-3 text-sm text-green-700">
                    {{ session('success') }}
                </div>
            @endif


            @if ($notifications->count())
                <div class="space-y-3">

                    @foreach ($notifications as $notification)
                        @php

                            $data = $notification->data;

                            $type = $data['type'] ?? 'general';

                            $isUnread = is_null($notification->read_at);

                            $title = $data['title'] ?? 'Notifikasi';

                            $message = $data['message'] ?? '';

                        @endphp


                        <div
                            class="overflow-hidden rounded-xl border
                                   bg-white shadow-sm
                                   {{ $isUnread ? 'border-indigo-200' : 'border-gray-200' }}">

                            <div class="flex">


                                {{-- ==================================================
                                ICON
                                =================================================== --}}

                                <div
                                    class="flex w-16 shrink-0 items-start
                                           justify-center pt-5">

                                    @if ($type === 'payment_pending')
                                        <div
                                            class="flex h-10 w-10 items-center
                                                    justify-center rounded-full
                                                    bg-yellow-100 text-yellow-600">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                    @elseif ($type === 'finance_deposit_created')
                                        <div
                                            class="flex h-10 w-10 items-center
                                                   justify-center rounded-full
                                                   bg-yellow-100 text-yellow-600">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                    @elseif ($type === 'finance_deposit_confirmed')
                                        <div
                                            class="flex h-10 w-10 items-center
                                                   justify-center rounded-full
                                                   bg-green-100 text-green-600">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                        </div>
                                    @elseif ($type === 'finance_deposit_rejected')
                                        <div
                                            class="flex h-10 w-10 items-center
                                                   justify-center rounded-full
                                                   bg-red-100 text-red-600">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </div>
                                    @else
                                        <div
                                            class="flex h-10 w-10 items-center
                                                   justify-center rounded-full
                                                   bg-indigo-100 text-indigo-600">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                            </svg>
                                        </div>
                                    @endif

                                </div>


                                {{-- ==================================================
                                CONTENT
                                =================================================== --}}

                                <div class="min-w-0 flex-1 px-4 py-4">

                                    <div class="flex items-start justify-between gap-4">

                                        <div class="min-w-0">

                                            <div class="flex items-center gap-2">

                                                <h3
                                                    class="text-sm
                                                        {{ $isUnread ? 'font-bold text-gray-900' : 'font-semibold text-gray-700' }}">
                                                    {{ $title }}
                                                </h3>


                                                @if ($isUnread)
                                                    <span
                                                        class="h-2 w-2 rounded-full
                                                               bg-indigo-600"
                                                        title="Belum dibaca"></span>
                                                @endif

                                            </div>


                                            <p class="mt-1 text-sm leading-6 text-gray-600">
                                                {{ $message }}
                                            </p>


                                            {{-- ======================================
                                            DETAIL SETORAN
                                            ======================================= --}}

                                            @if (in_array($type, ['finance_deposit_created', 'finance_deposit_confirmed', 'finance_deposit_rejected'], true))
                                                <div
                                                    class="mt-3 rounded-lg
                                                           bg-gray-50 p-3">

                                                    <div
                                                        class="grid grid-cols-1
                                                               gap-2 text-sm
                                                               sm:grid-cols-2">

                                                        @if (!empty($data['amount']))
                                                            <div>

                                                                <span class="text-gray-500">
                                                                    Jumlah
                                                                </span>

                                                                <div class="font-semibold text-gray-800">

                                                                    Rp
                                                                    {{ number_format($data['amount'], 0, ',', '.') }}

                                                                </div>

                                                            </div>
                                                        @endif


                                                        @if (!empty($data['deposit_date']))
                                                            <div>

                                                                <span class="text-gray-500">
                                                                    Tanggal Setoran
                                                                </span>

                                                                <div class="font-medium text-gray-800">

                                                                    {{ \Carbon\Carbon::parse($data['deposit_date'])->format('d/m/Y') }}

                                                                </div>

                                                            </div>
                                                        @endif


                                                        @if (!empty($data['payment_method']))
                                                            <div>

                                                                <span class="text-gray-500">
                                                                    Metode
                                                                </span>

                                                                <div class="font-medium text-gray-800">

                                                                    @switch($data['payment_method'])
                                                                        @case('cash')
                                                                            Tunai / Offline
                                                                        @break

                                                                        @case('bank_transfer')
                                                                            Transfer Bank
                                                                        @break

                                                                        @case('online')
                                                                            Online
                                                                        @break

                                                                        @default
                                                                            {{ $data['payment_method'] }}
                                                                    @endswitch

                                                                </div>

                                                            </div>
                                                        @endif


                                                        @if (!empty($data['description']))
                                                            <div>

                                                                <span class="text-gray-500">
                                                                    Keterangan
                                                                </span>

                                                                <div class="font-medium text-gray-800">
                                                                    {{ $data['description'] }}
                                                                </div>

                                                            </div>
                                                        @endif

                                                    </div>


                                                    {{-- Alasan penolakan --}}

                                                    @if ($type === 'finance_deposit_rejected' && !empty($data['rejection_reason']))
                                                        <div
                                                            class="mt-3 rounded-md
                                                                   border border-red-200
                                                                   bg-red-50 p-3">

                                                            <div
                                                                class="text-xs
                                                                       font-semibold
                                                                       uppercase
                                                                       text-red-600">
                                                                Alasan Penolakan
                                                            </div>

                                                            <div
                                                                class="mt-1 text-sm
                                                                       text-red-800">
                                                                {{ $data['rejection_reason'] }}
                                                            </div>

                                                        </div>
                                                    @endif

                                                </div>


                                                {{-- ==================================
                                                AKSI
                                                =================================== --}}

                                                @if (!empty($data['deposit_id']))
                                                    <div class="mt-3 flex flex-wrap gap-2">

                                                        <a href="{{ route('notifications.deposit-proof', $notification->id) }}"
                                                            target="_blank"
                                                            class="inline-flex items-center
                                                                   rounded-md
                                                                   bg-indigo-600
                                                                   px-3 py-2
                                                                   text-xs font-medium
                                                                   text-white
                                                                   hover:bg-indigo-700">

                                                            <svg class="mr-1.5 h-4 w-4" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />

                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                            </svg>

                                                            Lihat Slip Setoran

                                                        </a>

                                                    </div>
                                                @endif
                                            @endif



                                            @if ($type === 'payment_pending')
                                                <div class="mt-3 rounded-lg bg-gray-50 p-3">

                                                    <div class="grid grid-cols-1 gap-2 text-sm sm:grid-cols-2">

                                                        @if (!empty($data['payment_number']))
                                                            <div>

                                                                <span class="text-gray-500">
                                                                    Nomor Pembayaran
                                                                </span>

                                                                <div class="font-semibold text-gray-800">
                                                                    {{ $data['payment_number'] }}
                                                                </div>

                                                            </div>
                                                        @endif


                                                        @if (isset($data['amount']))
                                                            <div>

                                                                <span class="text-gray-500">
                                                                    Jumlah
                                                                </span>

                                                                <div class="font-semibold text-gray-800">

                                                                    Rp
                                                                    {{ number_format($data['amount'], 0, ',', '.') }}

                                                                </div>

                                                            </div>
                                                        @endif


                                                        @if (!empty($data['payment_date']))
                                                            <div>

                                                                <span class="text-gray-500">
                                                                    Tanggal Pembayaran
                                                                </span>

                                                                <div class="font-medium text-gray-800">

                                                                    {{ \Carbon\Carbon::parse($data['payment_date'])->format('d/m/Y H:i') }}

                                                                </div>

                                                            </div>
                                                        @endif


                                                        @if (!empty($data['payment_method']))
                                                            <div>

                                                                <span class="text-gray-500">
                                                                    Metode
                                                                </span>

                                                                <div class="font-medium text-gray-800">

                                                                    @switch($data['payment_method'])
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
                                                                            {{ $data['payment_method'] }}
                                                                    @endswitch

                                                                </div>

                                                            </div>
                                                        @endif

                                                        @if ($type === 'payment_pending' && !empty($data['payment_id']))
                                                            <div class="mt-3 flex flex-wrap gap-2">

                                                                <button type="button"
                                                                    class="inline-flex items-center
                                                                    rounded-md
                                                                    bg-indigo-600
                                                                    px-3 py-2
                                                                    text-xs font-medium
                                                                    text-white
                                                                    hover:bg-indigo-700
                                                                    btn-payment-notification-detail"
                                                                    data-bs-target="#paymentNotificationModal"
                                                                    data-payment-id="{{ $data['payment_id'] }}"
                                                                    data-notification-id="{{ $notification->id }}">

                                                                    <svg class="mr-1.5 h-4 w-4" fill="none"
                                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round"
                                                                            stroke-linejoin="round" stroke-width="2"
                                                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />

                                                                        <path stroke-linecap="round"
                                                                            stroke-linejoin="round" stroke-width="2"
                                                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542-7z" />
                                                                    </svg>

                                                                    Lihat Detail Pembayaran

                                                                </button>

                                                            </div>
                                                        @endif

                                                    </div>

                                                </div>
                                            @endif



                                            {{-- ======================================
                                            TRANSAKSI BIASA
                                            ======================================= --}}

                                            @if (in_array($type, ['finance_transaction_created', 'finance_transaction_cancelled'], true))
                                                <div class="mt-3 rounded-lg bg-gray-50 p-3">
                                                    <div class="grid grid-cols-1 gap-2 text-sm sm:grid-cols-2">

                                                        @if (!empty($data['amount']))
                                                            <div>

                                                                <span class="text-gray-500">
                                                                    Jumlah
                                                                </span>

                                                                <div
                                                                    class="font-semibold
                                                                           {{ ($data['type'] ?? null) === 'income' ? 'text-green-600' : 'text-red-600' }}">

                                                                    Rp
                                                                    {{ number_format($data['amount'], 0, ',', '.') }}

                                                                </div>

                                                            </div>
                                                        @endif


                                                        @if (!empty($data['category']))
                                                            <div>

                                                                <span class="text-gray-500">
                                                                    Kategori
                                                                </span>

                                                                <div class="font-medium text-gray-800">
                                                                    {{ $data['category'] }}
                                                                </div>

                                                            </div>
                                                        @endif

                                                        @if (!empty($data['transaction_id']))
                                                            <div>
                                                                <span class="text-gray-500">
                                                                    ID Transaksi
                                                                </span>

                                                                <div class="font-semibold text-gray-800">
                                                                    #{{ $data['transaction_id'] }}
                                                                </div>
                                                            </div>
                                                        @endif

                                                    </div>

                                                </div>
                                            @endif

                                        </div>


                                        {{-- ==========================================
                                        TANGGAL + BACA
                                        =========================================== --}}

                                        <div class="shrink-0 text-right">

                                            <div class="text-xs text-gray-400">

                                                {{ $notification->created_at->diffForHumans() }}

                                            </div>


                                            @if ($isUnread)
                                                <form method="POST"
                                                    action="{{ route('notifications.read', $notification->id) }}"
                                                    class="mt-2">

                                                    @csrf

                                                    <button type="submit"
                                                        class="text-xs font-medium
                                                               text-indigo-600
                                                               hover:text-indigo-800">
                                                        Tandai dibaca
                                                    </button>

                                                </form>
                                            @else
                                                <span
                                                    class="mt-2 inline-block
                                                           text-xs text-gray-400">
                                                    Sudah dibaca
                                                </span>
                                            @endif

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>
                    @endforeach

                </div>


                {{-- ==========================================================
                PAGINATION
                =========================================================== --}}

                <div class="mt-6">

                    {{ $notifications->links() }}

                </div>
            @else
                {{-- ==========================================================
                KOSONG
                =========================================================== --}}

                <div
                    class="rounded-xl border border-gray-200
                           bg-white px-6 py-16 text-center shadow-sm">

                    <div
                        class="mx-auto flex h-16 w-16 items-center
                               justify-center rounded-full bg-gray-100">

                        <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>

                    </div>

                    <h3 class="mt-4 text-sm font-semibold text-gray-800">
                        Tidak ada notifikasi
                    </h3>

                    <p class="mt-1 text-sm text-gray-500">
                        Belum ada pemberitahuan untuk Anda.
                    </p>

                </div>
            @endif

        </div>

    </div>
@endsection

@include('kepala-unit.notifications.partials.payment-detail-modal')
