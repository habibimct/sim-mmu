@props(['attendance'])

<div
    x-data="{ open: false }"
    x-on:open-teacher-attendance-detail-{{ $attendance->id }}.window="open = true"
    x-on:keydown.escape.window="open = false"
    x-show="open"
    x-cloak
    class="fixed inset-0 z-[90] overflow-y-auto"
>
    <div class="flex min-h-screen items-center justify-center p-4">

        {{-- Overlay --}}
        <div
            class="fixed inset-0 bg-black/50"
            @click="open = false"
        ></div>

        {{-- Modal --}}
        <div
            class="relative z-10 w-full max-w-4xl
                   overflow-hidden rounded-2xl
                   bg-white shadow-2xl"
            @click.stop
        >

            {{-- Header --}}
            <div
                class="flex items-center justify-between
                       border-b border-gray-200
                       px-6 py-4"
            >
                <div>
                    <h2 class="text-lg font-bold text-gray-800">
                        Detail Absensi Guru
                    </h2>

                    <p class="mt-1 text-sm text-gray-500">
                        Detail pelaksanaan pembelajaran dan kehadiran siswa.
                    </p>
                </div>

                <button
                    type="button"
                    @click="open = false"
                    class="rounded-lg p-2
                           text-gray-400
                           hover:bg-gray-100
                           hover:text-gray-600"
                >
                    &times;
                </button>
            </div>


            {{-- Identitas Pertemuan --}}
            <div class="border-b border-gray-200 px-6 py-5">

                <h3 class="mb-4 text-sm font-semibold text-gray-800">
                    Identitas Pertemuan
                </h3>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">

                    {{-- Guru --}}
                    <div>
                        <p class="text-xs text-gray-500">
                            Guru
                        </p>

                        <p class="mt-1 font-medium text-gray-800">
                            {{ $attendance->teachingAssignment->teacher->name }}
                        </p>
                    </div>

                    {{-- Unit --}}
                    <div>
                        <p class="text-xs text-gray-500">
                            Unit
                        </p>

                        <p class="mt-1 font-medium text-gray-800">
                            {{ $attendance->teachingAssignment->organization->name }}
                        </p>
                    </div>

                    {{-- Kelas --}}
                    <div>
                        <p class="text-xs text-gray-500">
                            Kelas
                        </p>

                        <p class="mt-1 font-medium text-gray-800">
                            {{ $attendance->teachingAssignment->schoolClass->name }}
                        </p>
                    </div>

                    {{-- Mata Pelajaran --}}
                    <div>
                        <p class="text-xs text-gray-500">
                            Mata Pelajaran
                        </p>

                        <p class="mt-1 font-medium text-gray-800">
                            {{ $attendance->teachingAssignment->subject->name }}
                        </p>
                    </div>

                    {{-- Tanggal --}}
                    <div>
                        <p class="text-xs text-gray-500">
                            Tanggal
                        </p>

                        <p class="mt-1 font-medium text-gray-800">
                            {{ $attendance->date->format('d/m/Y') }}
                        </p>
                    </div>

                    {{-- Jam --}}
                    <div>
                        <p class="text-xs text-gray-500">
                            Jam
                        </p>

                        <p class="mt-1 font-medium text-gray-800">
                            {{ $attendance->created_at->format('H:i') }}
                        </p>
                    </div>

                    {{-- Pertemuan --}}
                    <div>
                        <p class="text-xs text-gray-500">
                            Pertemuan
                        </p>

                        <p class="mt-1 font-medium text-gray-800">
                            Pertemuan ke-{{ $attendance->meeting_number }}
                        </p>
                    </div>

                    {{-- Jumlah Siswa --}}
                    <div>
                        <p class="text-xs text-gray-500">
                            Jumlah Siswa
                        </p>

                        <p class="mt-1 font-medium text-gray-800">
                            {{ $attendance->details_count }} siswa
                        </p>
                    </div>

                </div>


                {{-- Catatan Pertemuan --}}
                @if ($attendance->notes)
                    <div class="mt-4">
                        <p class="text-xs text-gray-500">
                            Catatan Pertemuan
                        </p>

                        <p class="mt-1 rounded-lg bg-gray-50 p-3 text-sm text-gray-700">
                            {{ $attendance->notes }}
                        </p>
                    </div>
                @endif

            </div>


            {{-- Daftar Siswa --}}
            <div class="px-6 py-5">

                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-800">
                        Daftar Kehadiran Siswa
                    </h3>

                    <span class="text-xs text-gray-500">
                        {{ $attendance->details_count }} siswa
                    </span>
                </div>


                <div class="overflow-hidden rounded-xl border border-gray-200">

                    <div class="max-h-[50vh] overflow-auto">

                        <table class="min-w-full text-sm">

                            <thead class="sticky top-0 bg-gray-50">
                                <tr class="border-b border-gray-200">

                                    <th class="px-4 py-3 text-left font-semibold text-gray-600">
                                        No
                                    </th>

                                    <th class="px-4 py-3 text-left font-semibold text-gray-600">
                                        NIS
                                    </th>

                                    <th class="px-4 py-3 text-left font-semibold text-gray-600">
                                        Nama Siswa
                                    </th>

                                    <th class="px-4 py-3 text-left font-semibold text-gray-600">
                                        Status
                                    </th>

                                    <th class="px-4 py-3 text-left font-semibold text-gray-600">
                                        Catatan
                                    </th>

                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-100">

                                @forelse ($attendance->details as $detail)

                                    <tr class="hover:bg-gray-50">

                                        <td class="px-4 py-3 text-gray-500">
                                            {{ $loop->iteration }}
                                        </td>

                                        <td class="px-4 py-3 text-gray-700">
                                            {{ $detail->studentAcademicYear->student->nis }}
                                        </td>

                                        <td class="px-4 py-3 font-medium text-gray-800">
                                            {{ $detail->studentAcademicYear->student->name }}
                                        </td>

                                        <td class="px-4 py-3">

                                            @switch($detail->status)

                                                @case('present')
                                                    <span class="inline-flex rounded-full bg-green-100 px-2.5 py-1 text-xs font-medium text-green-700">
                                                        Hadir
                                                    </span>
                                                    @break

                                                @case('sick')
                                                    <span class="inline-flex rounded-full bg-yellow-100 px-2.5 py-1 text-xs font-medium text-yellow-700">
                                                        Sakit
                                                    </span>
                                                    @break

                                                @case('permission')
                                                    <span class="inline-flex rounded-full bg-blue-100 px-2.5 py-1 text-xs font-medium text-blue-700">
                                                        Izin
                                                    </span>
                                                    @break

                                                @case('absent')
                                                    <span class="inline-flex rounded-full bg-red-100 px-2.5 py-1 text-xs font-medium text-red-700">
                                                        Alfa
                                                    </span>
                                                    @break

                                                @default
                                                    <span class="text-gray-500">
                                                        {{ $detail->status }}
                                                    </span>

                                            @endswitch

                                        </td>

                                        <td class="px-4 py-3 text-gray-600">
                                            {{ $detail->notes ?: '-' }}
                                        </td>

                                    </tr>

                                @empty

                                    <tr>
                                        <td
                                            colspan="5"
                                            class="px-4 py-8 text-center text-sm text-gray-500"
                                        >
                                            Belum ada data siswa.
                                        </td>
                                    </tr>

                                @endforelse

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>


            {{-- Footer --}}
            <div
                class="flex justify-end
                       border-t border-gray-200
                       px-6 py-4"
            >
                <button
                    type="button"
                    @click="open = false"
                    class="rounded-lg
                           border border-gray-300
                           bg-white
                           px-4 py-2.5
                           text-sm font-medium
                           text-gray-700
                           hover:bg-gray-50"
                >
                    Tutup
                </button>
            </div>

        </div>
    </div>
</div>
