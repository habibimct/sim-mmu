@extends('layouts.guru')

@section('content')
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="mt-1 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <h1 class="text-2xl font-bold text-gray-800">
                Dashboard Guru
            </h1>
            <div class="w-full sm:w-auto">
                @include('components.pwa-install-button')
            </div>

        </div>
        <p class="mt-2 text-gray-600">
            Alhamdulillah, login Guru berhasil.
        </p>
    </div>
@endsection
