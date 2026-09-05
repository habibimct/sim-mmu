@extends('layouts.guru')

@section('content')
    <div class="space-y-6">

        {{-- Header --}}
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    Absensi Guru
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Riwayat kehadiran Guru dalam melaksanakan pembelajaran di kelas.
                </p>
            </div>

            <button type="button" @click="$dispatch('open-teacher-attendance-filter')"
                class="inline-flex items-center gap-2 rounded-lg
                        bg-blue-600 px-4 py-2.5
                        text-sm font-medium text-white
                        shadow-sm
                        transition
                        hover:bg-blue-700
                        focus:outline-none
                        focus:ring-2 focus:ring-blue-500/30">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h18M6 12h12m-9 7h6" />
                </svg>

                Filter
            </button>
        </div>

        {{-- Chart Absensi Mingguan --}}
        @include('guru.teacher-attendance.partials.weekly-chart')

        {{-- Daftar Kehadiran --}}
        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">

            <div class="overflow-x-auto">

                <table class="min-w-full text-sm">

                    <thead class="bg-gray-50 border-b border-gray-200">

                        <tr>
                            <th class="px-4 py-3 text-left font-semibold text-gray-600">
                                No
                            </th>

                            <th class="px-4 py-3 text-left font-semibold text-gray-600">
                                Tanggal
                            </th>

                            <th class="px-4 py-3 text-left font-semibold text-gray-600">
                                Jam
                            </th>

                            <th class="px-4 py-3 text-left font-semibold text-gray-600">
                                Unit
                            </th>

                            <th class="px-4 py-3 text-left font-semibold text-gray-600">
                                Kelas
                            </th>

                            <th class="px-4 py-3 text-left font-semibold text-gray-600">
                                Mata Pelajaran
                            </th>

                            <th class="px-4 py-3 text-center font-semibold text-gray-600">
                                Pertemuan
                            </th>

                            <th class="px-4 py-3 text-center font-semibold text-gray-600">
                                Siswa
                            </th>

                            <th class="px-4 py-3 text-center font-semibold text-gray-600">
                                Aksi
                            </th>

                        </tr>

                    </thead>

                    <tbody class="divide-y divide-gray-100">

                        @forelse ($attendances as $attendance)
                            <tr class="hover:bg-gray-50">

                                <td class="px-4 py-3 text-gray-500">
                                    {{ $loop->iteration }}
                                </td>

                                <td class="px-4 py-3 text-gray-700">
                                    {{ $attendance->date->format('d/m/Y') }}
                                </td>

                                <td class="px-4 py-3 text-gray-700">
                                    {{ $attendance->created_at->format('H:i') }}
                                </td>

                                <td class="px-4 py-3 text-gray-700">
                                    {{ $attendance->teachingAssignment->organization->name }}
                                </td>

                                <td class="px-4 py-3 font-medium text-gray-800">
                                    {{ $attendance->teachingAssignment->schoolClass->name }}
                                </td>

                                <td class="px-4 py-3 text-gray-700">
                                    {{ $attendance->teachingAssignment->subject->name }}
                                </td>

                                <td class="px-4 py-3 text-center text-gray-700">
                                    {{ $attendance->meeting_number }}
                                </td>

                                <td class="px-4 py-3 text-center text-gray-700">
                                    {{ $attendance->details_count }}
                                </td>

                                <td class="px-4 py-3 text-center">
                                    <button type="button"
                                        @click="$dispatch('open-teacher-attendance-detail-{{ $attendance->id }}')"
                                        class="inline-flex items-center gap-1.5 rounded-lg
                                            bg-blue-50 px-3 py-2
                                            text-xs font-semibold text-blue-700
                                            transition hover:bg-blue-100">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5
                                                            c4.477 0 8.268 2.943 9.542 7
                                                            -1.274 4.057-5.065 7-9.542 7
                                                            -4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>

                                        Detail
                                    </button>
                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="9" class="px-4 py-10 text-center text-gray-500">
                                    Belum ada data kehadiran mengajar.
                                </td>

                            </tr>
                        @endforelse

                    </tbody>

                </table>
            </div>

            {{-- Modal Detail --}}
            @foreach ($attendances as $attendance)
                @include('guru.teacher-attendance.partials.detail-modal', ['attendance' => $attendance])
            @endforeach
        </div>
    </div>


    @include('guru.teacher-attendance.partials.filter-modal')
@endsection
