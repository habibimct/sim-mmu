@extends('layouts.kepala-unit')

@section('content')

<div class="max-w-7xl mx-auto">

    {{-- ==========================================================
    HEADER
    =========================================================== --}}

    <div class="mb-6">

        <h1 class="text-2xl font-bold text-gray-800">
            Absensi Siswa
        </h1>

        <p class="mt-1 text-sm text-gray-500">
            Monitoring kehadiran siswa per hari.
        </p>

        <p class="mt-1 text-sm font-medium text-gray-700">
            {{ \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y') }}
        </p>

    </div>


    {{-- ==========================================================
    FILTER
    =========================================================== --}}

    @include(
        'kepala-unit.student-attendances.partials.filters'
    )


    {{-- ==========================================================
    STATISTIK
    =========================================================== --}}

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">

        {{-- Hadir --}}
        <div
            class="bg-white
                   rounded-xl
                   border border-gray-200
                   shadow-sm
                   p-4"
        >

            <p class="text-sm text-gray-500">
                Hadir
            </p>

            <p class="mt-1 text-2xl font-bold text-gray-800">
                {{ $statistics['present'] }}
            </p>

        </div>


        {{-- Sakit --}}
        <div
            class="bg-white
                   rounded-xl
                   border border-gray-200
                   shadow-sm
                   p-4"
        >

            <p class="text-sm text-gray-500">
                Sakit
            </p>

            <p class="mt-1 text-2xl font-bold text-gray-800">
                {{ $statistics['sick'] }}
            </p>

        </div>


        {{-- Izin --}}
        <div
            class="bg-white
                   rounded-xl
                   border border-gray-200
                   shadow-sm
                   p-4"
        >

            <p class="text-sm text-gray-500">
                Izin
            </p>

            <p class="mt-1 text-2xl font-bold text-gray-800">
                {{ $statistics['permission'] }}
            </p>

        </div>


        {{-- Alpa --}}
        <div
            class="bg-white
                   rounded-xl
                   border border-gray-200
                   shadow-sm
                   p-4"
        >

            <p class="text-sm text-gray-500">
                Alpa
            </p>

            <p class="mt-1 text-2xl font-bold text-gray-800">
                {{ $statistics['absent'] }}
            </p>

        </div>

    </div>


    {{-- ==========================================================
    DATA
    =========================================================== --}}

    <div class="mt-6 space-y-6">

        @forelse ($attendances as $attendance)

            @php
                $assignment = $attendance->teachingAssignment;
            @endphp


            <div
                class="bg-white
                       rounded-xl
                       border border-gray-200
                       shadow-sm
                       overflow-hidden"
            >

                {{-- Header Pertemuan --}}
                <div
                    class="px-6 py-4
                           bg-gray-50
                           border-b border-gray-200"
                >

                    <div
                        class="flex flex-col
                               lg:flex-row
                               lg:items-center
                               lg:justify-between
                               gap-3"
                    >

                        <div>

                            <h2
                                class="font-semibold
                                       text-gray-800"
                            >
                                {{ $assignment?->schoolClass?->name ?? '-' }}
                                —
                                {{ $assignment?->subject?->name ?? '-' }}
                            </h2>

                            <p class="mt-1 text-sm text-gray-500">

                                Guru:
                                <span class="font-medium text-gray-700">
                                    {{ $assignment?->teacher?->name ?? '-' }}
                                </span>

                                ·

                                Pertemuan
                                <span class="font-medium text-gray-700">
                                    {{ $attendance->meeting_number }}
                                </span>

                            </p>

                        </div>


                        {{-- Jam Isi --}}
                        <div class="text-sm text-gray-500">

                            Jam isi:

                            <span class="font-semibold text-gray-700">
                                {{ $attendance->created_at?->format('H:i') ?? '-' }}
                            </span>

                        </div>

                    </div>

                </div>


                {{-- Daftar Siswa --}}
                <div class="overflow-x-auto">

                    <table class="min-w-full divide-y divide-gray-200">

                        <thead class="bg-white">

                            <tr>

                                <th
                                    class="px-6 py-3
                                           text-left
                                           text-xs
                                           font-semibold
                                           text-gray-500
                                           uppercase"
                                >
                                    No
                                </th>

                                <th
                                    class="px-6 py-3
                                           text-left
                                           text-xs
                                           font-semibold
                                           text-gray-500
                                           uppercase"
                                >
                                    NIS
                                </th>

                                <th
                                    class="px-6 py-3
                                           text-left
                                           text-xs
                                           font-semibold
                                           text-gray-500
                                           uppercase"
                                >
                                    Nama Siswa
                                </th>

                                <th
                                    class="px-6 py-3
                                           text-left
                                           text-xs
                                           font-semibold
                                           text-gray-500
                                           uppercase"
                                >
                                    Status
                                </th>

                                <th
                                    class="px-6 py-3
                                           text-left
                                           text-xs
                                           font-semibold
                                           text-gray-500
                                           uppercase"
                                >
                                    Catatan
                                </th>

                            </tr>

                        </thead>


                        <tbody class="divide-y divide-gray-200">

                            @forelse (
                                $attendance->details
                                as $detail
                            )

                                @php
                                    $student =
                                        $detail->studentAcademicYear?->student;
                                @endphp

                                <tr class="hover:bg-gray-50">

                                    {{-- No --}}
                                    <td
                                        class="px-6 py-4
                                               text-sm
                                               text-gray-500"
                                    >
                                        {{ $loop->iteration }}
                                    </td>


                                    {{-- NIS --}}
                                    <td
                                        class="px-6 py-4
                                               text-sm
                                               text-gray-700"
                                    >
                                        {{ $student?->nis ?? '-' }}
                                    </td>


                                    {{-- Nama --}}
                                    <td
                                        class="px-6 py-4
                                               text-sm
                                               font-medium
                                               text-gray-900"
                                    >
                                        {{ $student?->name ?? '-' }}
                                    </td>


                                    {{-- Status --}}
                                    <td class="px-6 py-4">

                                        @switch($detail->status)

                                            @case('present')

                                                <span
                                                    class="inline-flex
                                                           items-center
                                                           px-2.5 py-1
                                                           rounded-full
                                                           text-xs
                                                           font-medium
                                                           bg-green-100
                                                           text-green-700"
                                                >
                                                    Hadir
                                                </span>

                                                @break


                                            @case('sick')

                                                <span
                                                    class="inline-flex
                                                           items-center
                                                           px-2.5 py-1
                                                           rounded-full
                                                           text-xs
                                                           font-medium
                                                           bg-yellow-100
                                                           text-yellow-700"
                                                >
                                                    Sakit
                                                </span>

                                                @break


                                            @case('permission')

                                                <span
                                                    class="inline-flex
                                                           items-center
                                                           px-2.5 py-1
                                                           rounded-full
                                                           text-xs
                                                           font-medium
                                                           bg-blue-100
                                                           text-blue-700"
                                                >
                                                    Izin
                                                </span>

                                                @break


                                            @case('absent')

                                                <span
                                                    class="inline-flex
                                                           items-center
                                                           px-2.5 py-1
                                                           rounded-full
                                                           text-xs
                                                           font-medium
                                                           bg-red-100
                                                           text-red-700"
                                                >
                                                    Alpa
                                                </span>

                                                @break


                                            @default

                                                <span
                                                    class="text-sm
                                                           text-gray-500"
                                                >
                                                    -
                                                </span>

                                        @endswitch

                                    </td>


                                    {{-- Catatan --}}
                                    <td
                                        class="px-6 py-4
                                               text-sm
                                               text-gray-600"
                                    >
                                        {{ $detail->notes ?: '-' }}
                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td
                                        colspan="5"
                                        class="px-6 py-8
                                               text-center
                                               text-sm
                                               text-gray-400"
                                    >
                                        Belum ada data siswa
                                        pada pertemuan ini.
                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        @empty

            <div
                class="bg-white
                       rounded-xl
                       border border-gray-200
                       shadow-sm
                       p-12
                       text-center"
            >

                <p class="text-sm text-gray-400">
                    Belum ada data absensi siswa
                    pada tanggal ini.
                </p>

            </div>

        @endforelse

    </div>


    {{-- ==========================================================
    PAGINATION
    =========================================================== --}}

    @if ($attendances->hasPages())

        <div
            class="mt-6
                   bg-white
                   rounded-xl
                   border border-gray-200
                   p-4"
        >

            {{ $attendances->links() }}

        </div>

    @endif

</div>

@endsection
