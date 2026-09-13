<header
    class="h-16
           bg-white
           border-b border-gray-200
           flex items-center
           justify-between
           px-4 sm:px-6">

    {{-- ==================================================
    MOBILE MENU
    =================================================== --}}

    <button type="button" @click="sidebarOpen = true"
        class="lg:hidden
               inline-flex
               items-center
               justify-center
               p-2
               rounded-lg
               text-gray-500
               hover:bg-gray-100">

        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">

            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />

        </svg>

    </button>


    {{-- ==================================================
    JUDUL
    =================================================== --}}

    <div class="hidden sm:block">

        <h1 class="text-lg font-semibold text-gray-800">
            Kepala Unit
        </h1>

    </div>


    {{-- ==================================================
    NOTIFIKASI + USER
    =================================================== --}}

    <div class="flex items-center gap-3 ml-auto" x-data="{ notificationOpen: false }">

        {{-- ==================================================
        NOTIFICATION BELL
        =================================================== --}}

        <div class="relative">

            <button type="button" @click="notificationOpen = !notificationOpen"
                @click.outside="notificationOpen = false"
                class="relative
                       inline-flex
                       h-10
                       w-10
                       items-center
                       justify-center
                       rounded-xl
                       text-gray-500
                       transition
                       hover:bg-gray-100
                       hover:text-blue-600
                       focus:outline-none
                       focus:ring-2
                       focus:ring-blue-500
                       focus:ring-offset-2"
                aria-label="Notifikasi" :aria-expanded="notificationOpen">

                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />

                </svg>


                @php
                    $unreadNotifications = Auth::user()->unreadNotifications()->count();
                @endphp

                @if ($unreadNotifications > 0)
                    <span
                        class="absolute
                               -right-0.5
                               -top-0.5
                               min-w-[18px]
                               rounded-full
                               bg-red-500
                               px-1
                               text-center
                               text-[10px]
                               font-bold
                               leading-[18px]
                               text-white
                               ring-2
                               ring-white">
                        {{ $unreadNotifications > 99 ? '99+' : $unreadNotifications }}
                    </span>
                @endif

            </button>


            {{-- ==================================================
            NOTIFICATION DROPDOWN
            =================================================== --}}

            <div x-cloak x-show="notificationOpen" x-transition
                class="absolute
                       right-0
                       z-50
                       mt-3
                       w-80
                       overflow-hidden
                       rounded-2xl
                       border
                       border-gray-200
                       bg-white
                       shadow-xl">

                {{-- Header --}}

                <div
                    class="flex
                           items-center
                           justify-between
                           border-b
                           border-gray-100
                           px-4
                           py-3">

                    <div>

                        <h3
                            class="text-sm
                                   font-semibold
                                   text-gray-800">
                            Notifikasi
                        </h3>

                        <p class="text-xs text-gray-500">
                            {{ $unreadNotifications }} belum dibaca
                        </p>

                    </div>


                    @if ($unreadNotifications > 0)
                        <form method="POST" action="{{ route('notifications.read-all') }}">

                            @csrf

                            <button type="submit"
                                class="text-xs
                                       font-medium
                                       text-blue-600
                                       hover:text-blue-800">
                                Tandai semua dibaca
                            </button>

                        </form>
                    @endif

                </div>


                {{-- Notification list --}}

                <div class="max-h-96 overflow-y-auto">

                    @php
                        $navbarNotifications = Auth::user()->notifications()->latest()->limit(5)->get();
                    @endphp


                    @forelse ($navbarNotifications as $notification)
                        @php

                            $data = $notification->data;

                            $type = $data['type'] ?? null;

                            $title = $data['title'] ?? 'Notifikasi';

                            $message = $data['message'] ?? '';

                            $icon = match ($type) {
                                'finance_deposit_created' => 'text-amber-500',

                                'finance_deposit_confirmed' => 'text-green-500',

                                'finance_deposit_rejected' => 'text-red-500',

                                'finance_transaction_created' => 'text-blue-500',

                                'finance_transaction_cancelled' => 'text-red-500',

                                'payment_pending' => 'text-amber-500',

                                'student_payment_confirmed' => 'text-green-500',

                                'student_payment_cancelled' => 'text-red-500',

                                default => 'text-gray-400',
                            };

                        @endphp


                        <a href="{{ route('kepala-unit.notifications.index') }}"
                            class="block
                                   border-b
                                   border-gray-100
                                   px-4
                                   py-3
                                   transition
                                   hover:bg-gray-50
                                   {{ $notification->read_at ? 'bg-white' : 'bg-blue-50/50' }}">

                            <div class="flex gap-3">

                                <div class="mt-0.5 shrink-0">

                                    <svg class="h-5 w-5 {{ $icon }}" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">

                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />

                                    </svg>

                                </div>


                                <div class="min-w-0 flex-1">

                                    <p
                                        class="text-sm
                                            {{ $notification->read_at ? 'font-medium text-gray-700' : 'font-semibold text-gray-900' }}">
                                        {{ $title }}
                                    </p>

                                    <p
                                        class="mt-0.5
                                               text-xs
                                               leading-5
                                               text-gray-500">
                                        {{ $message }}
                                    </p>

                                    <p
                                        class="mt-1
                                               text-[11px]
                                               text-gray-400">
                                        {{ $notification->created_at->diffForHumans() }}
                                    </p>

                                </div>


                                @if (!$notification->read_at)
                                    <span
                                        class="mt-2
                                               h-2
                                               w-2
                                               shrink-0
                                               rounded-full
                                               bg-blue-600"></span>
                                @endif

                            </div>

                        </a>

                    @empty

                        <div
                            class="px-4
                                   py-8
                                   text-center
                                   text-sm
                                   text-gray-500">
                            Belum ada notifikasi.
                        </div>
                    @endforelse

                </div>


                {{-- Footer --}}

                <div class="border-t
                           border-gray-100
                           bg-gray-50">

                    <a href="{{ route('kepala-unit.notifications.index') }}"
                        class="block
                               px-4
                               py-3
                               text-center
                               text-sm
                               font-medium
                               text-blue-600
                               transition
                               hover:bg-gray-100
                               hover:text-blue-800">
                        Lihat semua notifikasi
                    </a>

                </div>

            </div>

        </div>


        {{-- ==================================================
        USER
        =================================================== --}}

        <div x-data="{ userMenuOpen: false }" class="relative">

            <button type="button" @click="userMenuOpen = !userMenuOpen"
                class="flex items-center gap-3
                       rounded-lg
                       px-2 py-1.5
                       hover:bg-gray-50
                       transition">

                <div class="text-right">

                    <p class="text-sm font-medium text-gray-800">
                        {{ Auth::user()->name }}
                    </p>

                    <p class="text-xs text-gray-500">
                        Kepala Unit
                    </p>

                </div>

                {{-- Avatar --}}
                <div
                    class="w-9 h-9
           rounded-full
           bg-blue-100
           overflow-hidden
           flex items-center
           justify-center">
                    @if (Auth::user()->profile_photo_path)
                        <img src="{{ asset('storage/' . Auth::user()->profile_photo_path) }}"
                            alt="{{ Auth::user()->name }}" class="w-full h-full object-cover">
                    @else
                        <span class="text-sm font-semibold text-blue-700">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </span>
                    @endif
                </div>


                <svg class="w-4 h-4
                           text-gray-400
                           transition-transform"
                    :class="{
                        'rotate-180': userMenuOpen
                    }" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">

                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 9 6 6 6-6" />

                </svg>

            </button>


            {{-- User menu --}}

            <div x-cloak x-show="userMenuOpen" @click.outside="userMenuOpen = false" x-transition
                class="absolute
                       right-0
                       mt-2
                       w-48
                       rounded-xl
                       bg-white
                       border
                       border-gray-100
                       shadow-lg
                       z-50
                       overflow-hidden">

                <div class="px-4 py-3
                           border-b border-gray-100">

                    <p
                        class="text-sm
                               font-semibold
                               text-gray-800
                               truncate">
                        {{ Auth::user()->name }}
                    </p>

                    <p
                        class="text-xs
                               text-gray-500
                               truncate">
                        {{ Auth::user()->email }}
                    </p>

                </div>


                <form method="POST" action="{{ route('logout') }}">

                    @csrf

                    <button type="submit"
                        class="w-full
                               flex items-center
                               gap-3
                               px-4 py-3
                               text-sm
                               text-red-600
                               hover:bg-red-50
                               transition">

                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                d="M15 12H3m0 0 4-4m-4 4 4 4M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4" />

                        </svg>

                        <span>
                            Keluar
                        </span>

                    </button>

                </form>

            </div>

        </div>

    </div>

</header>
