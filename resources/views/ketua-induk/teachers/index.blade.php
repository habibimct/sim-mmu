@extends('layouts.ketua-induk')

@section('content')

<div class="max-w-7xl mx-auto">

    {{-- Header --}}
    <div class="mb-6">

        <div class="flex flex-col sm:flex-row
                    sm:items-center sm:justify-between gap-3">

            <div>

                <h1 class="text-2xl font-bold text-gray-800">
                    Guru
                </h1>

                <p class="mt-1 text-sm text-gray-500">
                    Daftar guru dalam unit yang berada
                    dalam cakupan Ketua Induk.
                </p>

            </div>

            <div
                class="text-sm text-gray-500">
                Total:
                <span class="font-semibold text-gray-700">
                    {{ $teachers->total() }}
                </span>
                guru
            </div>

        </div>

    </div>


    {{-- Filter --}}
    @include('ketua-induk.teachers.partials.filters')


    {{-- Tabel --}}
    <div class="mt-6 bg-white rounded-xl shadow-sm
                border border-gray-200 overflow-hidden">

        <div class="overflow-x-auto">

            <table class="min-w-full divide-y divide-gray-200">

                <thead class="bg-gray-50">

                    <tr>

                        <th class="px-6 py-3 text-left
                                   text-xs font-semibold
                                   text-gray-500 uppercase">
                            No
                        </th>

                        <th class="px-6 py-3 text-left
                                   text-xs font-semibold
                                   text-gray-500 uppercase">
                            NIK
                        </th>

                        <th class="px-6 py-3 text-left
                                   text-xs font-semibold
                                   text-gray-500 uppercase">
                            Nama Guru
                        </th>

                        <th class="px-6 py-3 text-left
                                   text-xs font-semibold
                                   text-gray-500 uppercase">
                            Jenis Kelamin
                        </th>

                        <th class="px-6 py-3 text-left
                                   text-xs font-semibold
                                   text-gray-500 uppercase">
                            Unit
                        </th>

                        <th class="px-6 py-3 text-left
                                   text-xs font-semibold
                                   text-gray-500 uppercase">
                            Status
                        </th>

                    </tr>

                </thead>


                <tbody class="bg-white divide-y divide-gray-200">

                    @forelse ($teachers as $teacher)

                        <tr class="hover:bg-gray-50">

                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $teachers->firstItem() + $loop->index }}
                            </td>

                            <td class="px-6 py-4 text-sm text-gray-700">
                                {{ $teacher->nik ?: '-' }}
                            </td>

                            <td class="px-6 py-4">

                                <div class="font-medium text-gray-900">
                                    {{ $teacher->name }}
                                </div>

                                @if ($teacher->email)
                                    <div class="text-xs text-gray-500 mt-1">
                                        {{ $teacher->email }}
                                    </div>
                                @endif

                            </td>

                            <td class="px-6 py-4 text-sm text-gray-700">
                                {{ $teacher->gender ?: '-' }}
                            </td>

                            <td class="px-6 py-4">

                                <div class="flex flex-wrap gap-1">

                                    @forelse ($teacher->organizations as $organization)

                                        <span
                                            class="inline-flex items-center
                                                   px-2 py-1 rounded-md
                                                   text-xs font-medium
                                                   bg-gray-100 text-gray-700">
                                            {{ $organization->name }}
                                        </span>

                                    @empty

                                        <span class="text-sm text-gray-400">
                                            -
                                        </span>

                                    @endforelse

                                </div>

                            </td>

                            <td class="px-6 py-4">

                                @if ($teacher->is_active)

                                    <span
                                        class="inline-flex items-center
                                               px-2.5 py-1 rounded-full
                                               text-xs font-medium
                                               bg-green-100 text-green-700">
                                        Aktif
                                    </span>

                                @else

                                    <span
                                        class="inline-flex items-center
                                               px-2.5 py-1 rounded-full
                                               text-xs font-medium
                                               bg-gray-100 text-gray-600">
                                        Tidak Aktif
                                    </span>

                                @endif

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="6"
                                class="px-6 py-12 text-center">

                                <div class="text-gray-400">

                                    <p class="text-sm">
                                        Belum ada data guru.
                                    </p>

                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- Pagination --}}
        @if ($teachers->hasPages())

            <div class="px-6 py-4 border-t border-gray-200">

                {{ $teachers->links() }}

            </div>

        @endif

    </div>

</div>

@endsection
