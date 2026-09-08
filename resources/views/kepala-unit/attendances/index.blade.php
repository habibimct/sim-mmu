@extends('layouts.kepala-unit')

@section('content')
    <div class="max-w-7xl mx-auto">

        {{-- Header --}}
        <div class="mb-6">

            <h1 class="text-2xl font-bold text-gray-800">
                Absensi Guru
            </h1>

            <p class="mt-1 text-sm text-gray-500">
                Monitoring absensi guru per hari.
            </p>

            <p class="mt-1 text-sm font-medium text-gray-700">
                {{ \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y') }}
            </p>

        </div>


        {{-- Filter --}}
        @include('kepala-unit.attendances.partials.filters')


        {{-- Ringkasan --}}
        <div class="mt-6">

            <div
                class="bg-white
                   rounded-xl
                   border border-gray-200
                   shadow-sm
                   p-4">

                <p class="text-sm text-gray-500">
                    Total Pertemuan
                </p>

                <p class="mt-1 text-2xl font-bold text-gray-800">
                    {{ $total }}
                </p>

                <p class="mt-1 text-xs text-gray-400">
                    {{ \Carbon\Carbon::parse($date)->translatedFormat('d F Y') }}
                </p>

            </div>

        </div>


        {{-- Tabel --}}
        <div
            class="mt-6
               bg-white
               rounded-xl
               border border-gray-200
               shadow-sm
               overflow-hidden">

            <div class="overflow-x-auto">

                <table class="min-w-full divide-y divide-gray-200">

                    <thead class="bg-gray-50">

                        <tr>

                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">
                                No
                            </th>

                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">
                                Jam
                            </th>

                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">
                                Guru
                            </th>

                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">
                                Kelas
                            </th>

                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">
                                Mata Pelajaran
                            </th>

                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">
                                Pertemuan
                            </th>

                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">
                                Catatan
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-gray-200">

                        @forelse ($attendances as $attendance)
                            @php
                                $assignment = $attendance->teachingAssignment;
                            @endphp

                            <tr class="hover:bg-gray-50">

                                {{-- No --}}
                                <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                    {{ $attendances->firstItem() + $loop->index }}
                                </td>


                                {{-- Jam Isi --}}
                                <td class="px-6 py-4 text-sm text-gray-700 whitespace-nowrap">

                                    {{ $attendance->created_at?->format('H:i') ?? '-' }}

                                </td>


                                {{-- Guru --}}
                                <td class="px-6 py-4">

                                    <div class="font-medium text-gray-900">
                                        {{ $assignment?->teacher?->name ?? '-' }}
                                    </div>

                                </td>


                                {{-- Kelas --}}
                                <td class="px-6 py-4 text-sm text-gray-700 whitespace-nowrap">

                                    {{ $assignment?->schoolClass?->name ?? '-' }}

                                </td>


                                {{-- Mata Pelajaran --}}
                                <td class="px-6 py-4 text-sm text-gray-700">

                                    {{ $assignment?->subject?->name ?? '-' }}

                                </td>


                                {{-- Pertemuan --}}
                                <td class="px-6 py-4 text-sm text-gray-700 whitespace-nowrap">

                                    {{ $attendance->meeting_number }}

                                </td>


                                {{-- Catatan --}}
                                <td class="px-6 py-4 text-sm text-gray-600 max-w-xs">

                                    {{ $attendance->notes ?: '-' }}

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="7"
                                    class="px-6 py-12
                                        text-center
                                        text-sm
                                        text-gray-400">
                                    Belum ada data absensi guru
                                    pada tanggal ini.
                                </td>

                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>


            {{-- Pagination --}}
            @if ($attendances->hasPages())
                <div class="px-6 py-4
                       border-t border-gray-200">

                    {{ $attendances->links() }}

                </div>
            @endif

        </div>

    </div>
@endsection
