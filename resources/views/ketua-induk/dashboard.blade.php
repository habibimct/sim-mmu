@extends('layouts.ketua-induk')

@section('content')
    <x-slot name="header">

        <div>

            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Dashboard Ketua INDUK
            </h2>

            <p class="mt-1 text-sm text-gray-500">
                Selamat datang, {{ auth()->user()->name }}
            </p>

        </div>

    </x-slot>


    <div class="py-6">

        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">


            {{-- Informasi INDUK --}}

            <div class="rounded-lg bg-white p-6 shadow-sm">

                <div class="text-sm text-gray-500">
                    Organisasi
                </div>

                <div class="mt-1 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

                    <div class="text-2xl font-bold text-gray-800">
                        {{ $induk->name }}
                    </div>

                    <div class="w-full sm:w-auto">
                        @include('components.pwa-install-button')
                    </div>

                </div>

                <div class="mt-2 text-sm text-gray-500">
                    {{ $units->count() }} unit aktif
                </div>

            </div>


            {{-- Akses Cepat --}}

            <div>

                <h3 class="mb-3 font-semibold text-gray-800">
                    Akses Cepat
                </h3>


                <div class="grid grid-cols-1 gap-5 md:grid-cols-3">


                    {{-- Keuangan --}}

                    <a href="{{ route('ketua-induk.finance.index') }}"
                        class="rounded-lg bg-white p-5 shadow-sm
                               transition hover:shadow-md">

                        <div class="text-2xl">
                            💰
                        </div>

                        <div class="mt-3 font-semibold text-gray-800">
                            Keuangan
                        </div>

                        <div class="mt-1 text-sm text-gray-500">
                            Ringkasan dan transaksi keuangan
                        </div>

                    </a>


                    {{-- Organisasi --}}

                    <div class="rounded-lg bg-white p-5 shadow-sm">

                        <div class="text-2xl">
                            🏫
                        </div>

                        <div class="mt-3 font-semibold text-gray-800">
                            Organisasi
                        </div>

                        <div class="mt-1 text-sm text-gray-500">
                            Daftar unit di bawah INDUK
                        </div>

                    </div>


                    {{-- Notifikasi --}}

                    <a href="{{ route('ketua-induk.notifications.index') }}"
                        class="rounded-lg bg-white p-5 shadow-sm
                               transition hover:shadow-md">

                        <div class="text-2xl">
                            🔔
                        </div>

                        <div class="mt-3 font-semibold text-gray-800">
                            Notifikasi
                        </div>

                        <div class="mt-1 text-sm text-gray-500">
                            Lihat pemberitahuan terbaru
                        </div>

                    </a>

                </div>

            </div>


            {{-- Daftar Unit --}}

            <div class="overflow-hidden rounded-lg bg-white shadow-sm">

                <div class="border-b border-gray-200 px-5 py-4">

                    <h3 class="font-semibold text-gray-800">
                        Unit di Bawah {{ $induk->name }}
                    </h3>

                </div>


                <div class="divide-y divide-gray-100">

                    @forelse ($units as $unit)
                        <div class="flex items-center justify-between px-5 py-4">

                            <div>

                                <div class="font-medium text-gray-800">
                                    {{ $unit->name }}
                                </div>

                                @if ($unit->type)
                                    <div class="text-sm text-gray-500">
                                        {{ $unit->type }}
                                    </div>
                                @endif

                            </div>

                            <span
                                class="rounded-full bg-green-100
                                       px-2.5 py-1 text-xs
                                       font-medium text-green-700">
                                Aktif
                            </span>

                        </div>

                    @empty

                        <div class="px-5 py-8 text-center text-sm text-gray-500">
                            Belum ada unit aktif.
                        </div>
                    @endforelse

                </div>

            </div>


            {{-- Notifikasi --}}

            <div class="overflow-hidden rounded-lg bg-white shadow-sm">

                <div
                    class="flex items-center justify-between
                            border-b border-gray-200 px-5 py-4">

                    <h3 class="font-semibold text-gray-800">
                        Notifikasi Terbaru
                    </h3>

                    <a href="{{ route('notifications.index') }}"
                        class="text-sm font-medium text-indigo-600
                               hover:text-indigo-800">
                        Lihat semua
                    </a>

                </div>


                @forelse ($notifications as $notification)
                    @php
                        $data = $notification->data;
                    @endphp

                    <a href="{{ route('notifications.index') }}"
                        class="block border-b border-gray-100
                               px-5 py-4 hover:bg-gray-50">

                        <div class="font-medium text-gray-800">
                            {{ $data['title'] ?? 'Notifikasi' }}
                        </div>

                        <div class="mt-1 text-sm text-gray-600">
                            {{ $data['message'] ?? '' }}
                        </div>

                        <div class="mt-1 text-xs text-gray-400">
                            {{ $notification->created_at->diffForHumans() }}
                        </div>

                    </a>

                @empty

                    <div class="px-5 py-8 text-center text-sm text-gray-500">
                        Belum ada notifikasi.
                    </div>
                @endforelse

            </div>


        </div>

    </div>
@endsection
