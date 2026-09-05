@php
    $user = auth()->user();

    $unreadNotifications = $user
        ->unreadNotifications()
        ->latest()
        ->take(10)
        ->get();

    $unreadCount = $user
        ->unreadNotifications()
        ->count();
@endphp

<div
    x-data="{ open: false }"
    class="relative"
>
    {{-- Tombol notifikasi --}}
    <button
        type="button"
        @click="open = !open"
        class="relative inline-flex items-center justify-center rounded-md
               p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-700
               focus:outline-none"
        aria-label="Notifikasi"
    >

        {{-- Bell --}}
        <svg
            class="h-6 w-6"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M15 17h5l-1.405-1.405A2.032
                   2.032 0 0118 14.158V11a6.002
                   6.002 0 00-4-5.659V5a2 2 0
                   10-4 0v.341C7.67 6.165
                   6 8.388 6 11v3.159c0
                   .538-.214 1.055-.595
                   1.436L4 17h5m6 0v1a3
                   3 0 11-6 0v-1m6 0H9"
            />
        </svg>


        {{-- Badge --}}
        @if ($unreadCount > 0)

            <span
                class="absolute -right-1 -top-1 min-w-[18px]
                       rounded-full bg-red-600 px-1 text-center
                       text-xs font-bold leading-[18px] text-white"
            >
                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
            </span>

        @endif

    </button>


    {{-- Dropdown --}}
    <div
        x-show="open"
        x-cloak
        @click.outside="open = false"
        class="absolute right-0 z-50 mt-2 w-96
               overflow-hidden rounded-lg bg-white shadow-lg
               ring-1 ring-black ring-opacity-5"
    >

        {{-- Header --}}
        <div
            class="flex items-center justify-between
                   border-b border-gray-200 px-4 py-3"
        >

            <div class="font-semibold text-gray-800">
                Notifikasi
            </div>

            @if ($unreadCount > 0)

                <span class="text-sm text-gray-500">
                    {{ $unreadCount }} belum dibaca
                </span>

            @endif

        </div>


        {{-- Daftar --}}
        <div class="max-h-96 overflow-y-auto">

            @forelse ($unreadNotifications as $notification)

                @php
                    $data = $notification->data;

                    $type = $data['type'] ?? null;
                @endphp


                <a
                    href="{{ route('notifications.index') }}"
                    class="block border-b border-gray-100 px-4 py-3
                           hover:bg-gray-50"
                >

                    <div class="flex gap-3">

                        {{-- Icon --}}
                        <div class="shrink-0">

                            @if ($type === 'finance_deposit_rejected')

                                <div
                                    class="flex h-9 w-9 items-center
                                           justify-center rounded-full
                                           bg-red-100 text-red-600"
                                >
                                    ✕
                                </div>

                            @elseif ($type === 'finance_deposit_confirmed')

                                <div
                                    class="flex h-9 w-9 items-center
                                           justify-center rounded-full
                                           bg-green-100 text-green-600"
                                >
                                    ✓
                                </div>

                            @else

                                <div
                                    class="flex h-9 w-9 items-center
                                           justify-center rounded-full
                                           bg-blue-100 text-blue-600"
                                >
                                    Rp
                                </div>

                            @endif

                        </div>


                        {{-- Content --}}
                        <div class="min-w-0 flex-1">

                            <div class="font-medium text-gray-800">

                                {{ $data['title'] ?? 'Notifikasi' }}

                            </div>

                            <div
                                class="mt-1 text-sm text-gray-600"
                            >

                                {{ $data['message'] ?? '' }}

                            </div>


                            @if (! empty($data['rejection_reason']))

                                <div
                                    class="mt-1 text-sm text-red-600"
                                >
                                    Alasan:
                                    {{ $data['rejection_reason'] }}
                                </div>

                            @endif


                            <div
                                class="mt-1 text-xs text-gray-400"
                            >

                                {{ $notification->created_at->diffForHumans() }}

                            </div>

                        </div>

                    </div>

                </a>

            @empty

                <div
                    class="px-4 py-8 text-center text-sm
                           text-gray-500"
                >
                    Tidak ada notifikasi baru.
                </div>

            @endforelse

        </div>


        {{-- Footer --}}
        <div class="border-t border-gray-200">

            <a
                href="{{ route('notifications.index') }}"
                class="block px-4 py-3 text-center text-sm
                       font-medium text-indigo-600
                       hover:bg-gray-50"
            >
                Lihat semua notifikasi
            </a>

        </div>

    </div>

</div>
