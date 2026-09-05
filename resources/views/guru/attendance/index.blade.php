@extends('layouts.guru')

@section('content')

    {{-- Header --}}
    <div class="mb-6">

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    Absensi Siswa
                </h1>

                <p class="mt-1 text-sm text-gray-500">
                    Daftar penugasan mengajar yang dapat digunakan untuk melakukan absensi.
                </p>
            </div>

            {{-- Tombol Filter --}}
            <button type="button" @click="$dispatch('open-attendance-filter')"
                class="inline-flex items-center justify-center gap-2
                   px-4 py-2.5
                   rounded-lg
                   bg-white
                   border border-gray-200
                   text-sm font-semibold text-gray-700
                   shadow-sm
                   hover:bg-gray-50
                   hover:border-gray-300
                   transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 5h18M6 12h12M10 19h4" />
                </svg>

                Filter
            </button>

        </div>


        {{-- Filter Aktif --}}
        <div class="mt-4 flex flex-wrap items-center gap-2">

            <span class="text-xs font-medium text-gray-500">
                Filter:
            </span>

            @if ($academicYearId === 'all')
                <span class="px-2.5 py-1 rounded-full bg-blue-100 text-blue-700 text-xs font-medium">
                    Semua Tahun Akademik
                </span>
            @else
                @php
                    $selectedAcademicYear = $academicYears->firstWhere('id', $academicYearId);
                @endphp

                @if ($selectedAcademicYear)
                    <span class="px-2.5 py-1 rounded-full bg-blue-100 text-blue-700 text-xs font-medium">
                        {{ $selectedAcademicYear->name }}
                    </span>
                @endif
            @endif


            @if ($schoolClassId && $schoolClassId !== 'all')
                @php
                    $selectedClass = $filterClasses->firstWhere('id', $schoolClassId);
                @endphp

                @if ($selectedClass)
                    <span class="px-2.5 py-1 rounded-full bg-indigo-100 text-indigo-700 text-xs font-medium">
                        Kelas: {{ $selectedClass->name }}
                    </span>
                @endif
            @else
                <span class="px-2.5 py-1 rounded-full bg-gray-100 text-gray-600 text-xs font-medium">
                    Semua Kelas
                </span>
            @endif


            @if ($subjectId && $subjectId !== 'all')
                @php
                    $selectedSubject = $filterSubjects->firstWhere('id', $subjectId);
                @endphp

                @if ($selectedSubject)
                    <span class="px-2.5 py-1 rounded-full bg-purple-100 text-purple-700 text-xs font-medium">
                        Mapel: {{ $selectedSubject->name }}
                    </span>
                @endif
            @else
                <span class="px-2.5 py-1 rounded-full bg-gray-100 text-gray-600 text-xs font-medium">
                    Semua Mapel
                </span>
            @endif

        </div>

    </div>


    @if (session('success'))
        <div x-data="{ show: true }" x-show="show" x-transition
            class="mb-6 flex items-center justify-between rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            <div class="flex items-center gap-2">
                <span class="font-semibold">✓</span>
                <span>{{ session('success') }}</span>
            </div>

            <button type="button" @click="show = false" class="text-lg leading-none text-green-700 hover:text-green-900">
                &times;
            </button>
        </div>
    @endif

    {{-- Daftar Penugasan --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3 sm:gap-4">

        @forelse ($teachingAssignments as $assignment)
            <div
                class="bg-white
                   rounded-lg
                   border border-gray-200
                   shadow-sm
                   overflow-hidden">

                {{-- Isi Kartu --}}
                <div class="p-3 sm:p-4">

                    {{-- Kelas + Status --}}
                    <div class="flex items-center justify-between gap-2">

                        <div class="min-w-0">
                            <p class="text-[11px] font-medium uppercase tracking-wide text-gray-400">
                                Kelas
                            </p>

                            <h2 class="mt-0.5 text-lg font-bold text-gray-800 truncate">
                                {{ $assignment->schoolClass->name }}
                            </h2>
                        </div>

                        <span
                            class="shrink-0
                               inline-flex items-center
                               px-2 py-0.5
                               rounded-full
                               text-[11px]
                               font-semibold
                               bg-green-100
                               text-green-700">
                            Aktif
                        </span>

                    </div>


                    {{-- Mata Pelajaran --}}
                    <div class="mt-3">

                        <p class="text-[11px] font-medium uppercase tracking-wide text-gray-400">
                            Mata Pelajaran
                        </p>

                        <p class="mt-0.5 text-sm font-semibold text-gray-800 truncate">
                            {{ $assignment->subject->name }}
                        </p>

                    </div>


                    {{-- Unit + Tahun Ajaran --}}
                    <div class="grid grid-cols-2 gap-3 mt-3">

                        <div class="min-w-0">

                            <p class="text-[11px] font-medium uppercase tracking-wide text-gray-400">
                                Unit
                            </p>

                            <p class="mt-0.5 text-xs text-gray-700 truncate">
                                {{ $assignment->schoolClass->organization->name }}
                            </p>

                        </div>

                        <div class="min-w-0">

                            <p class="text-[11px] font-medium uppercase tracking-wide text-gray-400">
                                Tahun Ajaran
                            </p>

                            <p class="mt-0.5 text-xs text-gray-700 truncate">
                                {{ $assignment->schoolClass->academicYear->name }}
                            </p>

                        </div>

                    </div>

                </div>


                {{-- Footer Kartu --}}
                <div class="px-3 py-2.5
                       bg-gray-50
                       border-t border-gray-100">

                    <button type="button"
                        @click="$dispatch('open-attendance-modal', {
                        id: {{ $assignment->id }},
                        class_name: @js($assignment->schoolClass->name),
                        subject_name: @js($assignment->subject->name),
                        academic_year_name: @js($assignment->schoolClass->academicYear->name)
                    })"
                        class="w-full
                           inline-flex
                           items-center
                           justify-center
                           gap-1.5
                           px-3 py-2
                           rounded-md
                           bg-blue-600
                           text-white
                           text-sm
                           font-semibold
                           hover:bg-blue-700
                           transition">

                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                d="M9 11h6M9 15h4M7 3h10v4H7zM5 5H4a1 1 0 0 0-1 1v14h18V6a1 1 0 0 0-1-1h-1" />
                        </svg>

                        Input Absensi

                    </button>

                </div>

            </div>

        @empty

            <div class="col-span-full">

                <div
                    class="bg-white
                       rounded-lg
                       border border-gray-200
                       shadow-sm
                       p-6
                       text-center">
                    <p class="text-sm text-gray-500">
                        Belum ada penugasan mengajar yang aktif.
                    </p>
                </div>

            </div>
        @endforelse

    </div>




    {{-- =========================================================
    RIWAYAT ABSENSI
    ========================================================= --}}

    <div class="mt-8">

        <div class="mb-4">
            <h2 class="text-lg font-bold text-gray-800">
                Riwayat Absensi
            </h2>

            <p class="mt-1 text-sm text-gray-500">
                Daftar absensi siswa yang telah dilakukan.
            </p>
        </div>

        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">

            <div class="overflow-x-auto">

                <table class="min-w-full text-sm">

                    <thead class="border-b border-gray-200 bg-gray-50">

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
                            @php
                                $canModify = $attendance->created_at->gte(now()->subHours(6));
                            @endphp

                            <tr class="transition hover:bg-gray-50">

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
                                    {{ $attendance->teachingAssignment->schoolClass->organization->name }}
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

                                <td class="px-4 py-3">

                                    <div class="flex items-center justify-center gap-2">

                                        {{-- Edit --}}
                                        @if ($canModify)
                                            <button type="button"
                                                @click="$dispatch(
                                                'open-attendance-edit',
                                                {
                                                    id: {{ $attendance->id }}
                                                }
                                            )"
                                                class="inline-flex items-center gap-1.5 rounded-lg bg-blue-50 px-3 py-2 text-xs font-semibold text-blue-700 transition hover:bg-blue-100">
                                                Edit
                                            </button>

                                            {{-- Hapus --}}
                                            <button type="button"
                                                @click="$dispatch(
                                                'open-attendance-delete',
                                                {
                                                    id: {{ $attendance->id }}
                                                }
                                            )"
                                                class="inline-flex items-center gap-1.5 rounded-lg bg-red-50 px-3 py-2 text-xs font-semibold text-red-700 transition hover:bg-red-100">
                                                Hapus
                                            </button>
                                        @else
                                            <span class="text-xs text-gray-400">
                                                Lewat 6 jam
                                            </span>
                                        @endif

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="9" class="px-4 py-10 text-center text-sm text-gray-500">
                                    Belum ada riwayat absensi.
                                </td>

                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>



    @include('guru.attendance.partials.filter-modal')
    @include('guru.attendance.partials.input-modal')
    @include('guru.attendance.partials.edit-modal')
    @include('guru.attendance.partials.delete-modal')

@endsection
