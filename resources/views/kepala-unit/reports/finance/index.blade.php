@extends('layouts.kepala-unit')

@section('content')

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-semibold text-gray-800">
                Laporan Keuangan
            </h2>

            <p class="mt-1 text-sm text-gray-500">
                {{ $organization->name }}
            </p>
        </div>

        <a href="{{ route('kepala-unit.dashboard') }}"
            class="rounded-md bg-gray-100 px-4 py-2 text-sm
                   font-medium text-gray-700 hover:bg-gray-200">
            ← Dashboard
        </a>
    </div>

    <div class="py-6">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">

            @include('kepala-unit.reports.finance.partials.filter')

            @include('kepala-unit.reports.finance.partials.summary')

            @include('kepala-unit.reports.finance.partials.deposit-summary')

            @include('kepala-unit.reports.finance.partials.transactions')

        </div>
    </div>

@endsection
