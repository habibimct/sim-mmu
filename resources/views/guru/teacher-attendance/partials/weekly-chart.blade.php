<div class="mb-6 bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">

    {{-- Header --}}
    <div class="px-4 py-3 border-b border-gray-100">

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

            {{-- Judul --}}
            <div>
                <h2 class="text-base font-bold text-gray-800">
                    Absensi Mingguan
                </h2>

                <p class="mt-0.5 text-xs text-gray-500">
                    {{ $weekStart->translatedFormat('d M Y') }}
                    –
                    {{ $weekEnd->translatedFormat('d M Y') }}
                </p>
            </div>


            {{-- Navigasi Minggu --}}
            <div class="flex items-center gap-1">

                {{-- Previous Week --}}
                <a href="{{ request()->fullUrlWithQuery([
                    'week' => $weekStart->copy()->subWeek()->format('Y-m-d'),
                ]) }}"
                    class="inline-flex items-center justify-center
                       w-9 h-9
                       rounded-lg
                       border border-gray-200
                       bg-white
                       text-gray-600
                       hover:bg-gray-50
                       hover:text-gray-800
                       transition"
                    title="Minggu sebelumnya">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>


                {{-- Minggu Ini --}}
                <a href="{{ request()->fullUrlWithQuery([
                    'week' => now()->format('Y-m-d'),
                ]) }}"
                    class="inline-flex items-center justify-center
                       px-3 h-9
                       rounded-lg
                       border border-gray-200
                       bg-white
                       text-xs font-semibold
                       text-gray-700
                       hover:bg-gray-50
                       transition">
                    Minggu Ini
                </a>


                {{-- Next Week --}}
                <a href="{{ request()->fullUrlWithQuery([
                    'week' => $weekStart->copy()->addWeek()->format('Y-m-d'),
                        ]) }}"
                            class="inline-flex items-center justify-center
                            w-9 h-9
                            rounded-lg
                            border border-gray-200
                            bg-white
                            text-gray-600
                            hover:bg-gray-50
                            hover:text-gray-800
                            transition"
                            title="Minggu berikutnya">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>

            </div>

        </div>

    </div>


    {{-- Chart --}}
    <div class="overflow-x-auto">

        <table class="w-full min-w-[900px] border-collapse">

            {{-- Header Hari --}}
            <thead>
                <tr class="bg-gray-50">

                    <th
                        class="w-20 px-3 py-3 text-center text-xs font-semibold
                               text-gray-500 border-b border-r border-gray-200">
                        Jam
                    </th>

                    @foreach ([
                                1 => 'Senin',
                                2 => 'Selasa',
                                3 => 'Rabu',
                                4 => 'Kamis',
                                5 => 'Jumat',
                                6 => 'Sabtu',
                                7 => 'Minggu',
                            ] as $dayNumber => $dayName)
                        <th
                            class="px-3 py-3 text-center text-xs font-semibold
                                   text-gray-600 border-b border-r border-gray-200">
                            {{ $dayName }}
                        </th>
                    @endforeach

                </tr>
            </thead>


            <tbody>

                @php
                    /*
                    |--------------------------------------------------------------------------
                    | Jam
                    |--------------------------------------------------------------------------
                    |
                    | Untuk sementara jam dibuat dari data created_at.
                    | Nanti akan diganti menggunakan Teacher Attendance.
                    |
                    */

                    $hours = $weeklyAttendances
                        ->map(function ($attendance) {
                            return $attendance->created_at->format('H:00');
                        })
                        ->unique()
                        ->sort()
                        ->values();
                @endphp


                @forelse ($hours as $hour)

                    <tr>

                        {{-- Jam --}}
                        <td
                            class="px-3 py-3 text-center align-top
                                   text-xs font-semibold text-gray-500
                                   bg-gray-50 border-b border-r border-gray-200">
                            {{ $hour }}
                        </td>


                        {{-- Hari --}}
                        @foreach (range(1, 7) as $dayNumber)
                            @php
                                $cellAttendances = $weeklyAttendances->filter(function ($attendance) use (
                                    $hour,
                                    $dayNumber,
                                ) {
                                    return $attendance->date->dayOfWeekIso === $dayNumber &&
                                        $attendance->created_at->format('H:00') === $hour;
                                });
                            @endphp


                            <td
                                class="p-2 align-top
                                       border-b border-r border-gray-200">

                                @forelse ($cellAttendances as $attendance)
                                    <div
                                        class="mb-2 last:mb-0
                                               rounded-lg
                                               border border-blue-100
                                               bg-blue-50
                                               px-3 py-2">

                                        {{-- Kelas --}}
                                        <div class="text-sm font-bold text-blue-800">
                                            {{ $attendance->teachingAssignment->schoolClass->name }}
                                        </div>


                                        {{-- Mata Pelajaran --}}
                                        <div class="mt-0.5 text-xs font-medium text-gray-700">
                                            {{ $attendance->teachingAssignment->subject->name }}
                                        </div>


                                        {{-- Pertemuan --}}
                                        <div class="mt-1 text-[11px] text-gray-500">
                                            Pertemuan ke-{{ $attendance->meeting_number }}
                                        </div>


                                        {{-- Jam Input --}}
                                        <div class="mt-1 text-[10px] text-gray-400">
                                            Input {{ $attendance->created_at->format('H:i') }}
                                        </div>

                                    </div>

                                @empty

                                    <div class="min-h-[72px]"></div>
                                @endforelse

                            </td>
                        @endforeach

                    </tr>

                    @empty

                        <tr>
                            <td colspan="8" class="px-4 py-10 text-center text-sm text-gray-500">
                                Belum ada absensi pada minggu ini.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>
