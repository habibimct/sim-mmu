@extends('layouts.kepala-unit')



@section('content')
    <x-slot name="header">

        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard Kepala Unit
            </h2>

            <p class="mt-1 text-sm text-gray-500">
                Ringkasan informasi unit Anda.
            </p>
        </div>

    </x-slot>


    <div class="py-8">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Sambutan --}}
            <div class="bg-white overflow-hidden shadow-sm rounded-xl">

                <div class="p-6">

                    <h3 class="text-lg font-semibold text-gray-800">
                        Selamat datang,
                        {{ Auth::user()->name }}
                    </h3>

                    <p class="mt-1 text-sm text-gray-500">
                        Anda masuk sebagai Kepala Unit.
                    </p>

                </div>

            </div>


            {{-- Informasi umum --}}
            <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

                {{-- Keuangan --}}
                <a
                    href="{{route('kepala-unit.finance.summary.index')}}"
                    class="bg-white rounded-xl shadow-sm p-6
                           hover:shadow-md transition
                           border border-gray-100"
                >

                    <div class="flex items-center justify-between">

                        <div>

                            <p class="text-sm text-gray-500">
                                Keuangan
                            </p>

                            <p class="mt-1 text-lg font-semibold text-gray-800">
                                Ringkasan Keuangan
                            </p>

                        </div>

                        <div
                            class="w-12 h-12 rounded-full
                                   bg-blue-100
                                   flex items-center justify-center"
                        >
                            <span class="text-blue-600 text-xl">
                                Rp
                            </span>
                        </div>

                    </div>

                    <p class="mt-4 text-sm text-gray-500">
                        Lihat transaksi dan ringkasan keuangan unit.
                    </p>

                </a>


                {{-- Notifikasi --}}
                <a
                    href="{{ route('kepala-unit.notifications.index') }}"
                    class="bg-white rounded-xl shadow-sm p-6
                           hover:shadow-md transition
                           border border-gray-100"
                >

                    <div class="flex items-center justify-between">

                        <div>

                            <p class="text-sm text-gray-500">
                                Notifikasi
                            </p>

                            <p class="mt-1 text-lg font-semibold text-gray-800">
                                Pemberitahuan
                            </p>

                        </div>

                        <div
                            class="w-12 h-12 rounded-full
                                   bg-yellow-100
                                   flex items-center justify-center"
                        >
                            <span class="text-yellow-600 text-xl">
                                🔔
                            </span>
                        </div>

                    </div>

                    <p class="mt-4 text-sm text-gray-500">
                        Lihat transaksi dan setoran terbaru.
                    </p>

                </a>

            </div>

        </div>

    </div>

@endsection
