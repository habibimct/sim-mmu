@extends('layouts.ketua-induk')

@section('content')

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-semibold text-gray-800">
                Laporan Keuangan
            </h2>

            <p class="mt-1 text-sm text-gray-500">
                {{ $induk->name }}
            </p>
        </div>

        <a href="{{ route('ketua-induk.dashboard') }}"
            class="rounded-md bg-gray-100 px-4 py-2 text-sm
                   font-medium text-gray-700 hover:bg-gray-200">
            ← Dashboard
        </a>
    </div>

    <div class="py-6">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">

            @include('ketua-induk.reports.finance.partials.filter')

            @include('ketua-induk.reports.finance.partials.summary')

            @include('ketua-induk.reports.finance.partials.organization-summary')

            @include('ketua-induk.reports.finance.partials.transactions')

        </div>
    </div>

@endsection
