@extends('layouts.wali')

@section('title', 'Kehadiran')

@section('content')
    <header class="mb-5">
        <p class="text-xs text-slate-500">SIM-MMU · Portal Orang Tua</p>
        <h1 class="mt-2 text-xl font-bold">Riwayat Kehadiran</h1>
        <p class="mt-1 text-sm text-slate-600">
            Catatan kehadiran {{ $student->name }}.
        </p>
    </header>

    <a href="{{ route('wali.dashboard') }}" class="mb-4 inline-flex items-center gap-2 text-sm font-medium text-emerald-700">
        <span aria-hidden="true">←</span>
        Kembali ke Beranda
    </a>

    <section class="mb-4 rounded-xl border border-slate-200 bg-white p-4">
        <p class="text-xs text-slate-500">Nama anak</p>
        <h2 class="mt-1 font-semibold">{{ $student->name }}</h2>
        <p class="mt-1 text-sm text-slate-500">
            Kelas
            {{ $student->studentAcademicYears->sortByDesc('id')->first()?->schoolClass?->name ?? '-' }}
        </p>
    </section>

    <section class="mb-5">
        <h2 class="mb-3 text-sm font-bold">Ringkasan Kehadiran</h2>

        <div class="grid grid-cols-2 gap-3">
            <div class="rounded-xl border border-emerald-100 bg-emerald-50 p-4">
                <p class="text-sm text-emerald-700">Hadir</p>
                <p class="mt-1 text-2xl font-bold text-emerald-800">
                    {{ $attendanceSummary['present'] }}
                </p>
            </div>

            <div class="rounded-xl border border-sky-100 bg-sky-50 p-4">
                <p class="text-sm text-sky-700">Sakit</p>
                <p class="mt-1 text-2xl font-bold text-sky-800">
                    {{ $attendanceSummary['sick'] }}
                </p>
            </div>

            <div class="rounded-xl border border-amber-100 bg-amber-50 p-4">
                <p class="text-sm text-amber-700">Izin</p>
                <p class="mt-1 text-2xl font-bold text-amber-800">
                    {{ $attendanceSummary['permission'] }}
                </p>
            </div>

            <div class="rounded-xl border border-red-100 bg-red-50 p-4">
                <p class="text-sm text-red-700">Alpa</p>
                <p class="mt-1 text-2xl font-bold text-red-800">
                    {{ $attendanceSummary['absent'] }}
                </p>
            </div>
        </div>
    </section>

    <section>
        <h2 class="mb-3 text-sm font-bold">Catatan Kehadiran</h2>

        @forelse ($attendanceDetails as $detail)
            @php
                $status = $detail->status;

                $statusLabels = [
                    'present' => 'Hadir',
                    'sick' => 'Sakit',
                    'permission' => 'Izin',
                    'absent' => 'Alpa',
                ];

                $statusClasses = [
                    'present' => 'bg-emerald-100 text-emerald-800',
                    'sick' => 'bg-sky-100 text-sky-800',
                    'permission' => 'bg-amber-100 text-amber-800',
                    'absent' => 'bg-red-100 text-red-800',
                ];
            @endphp

            <article class="mb-3 rounded-xl border border-slate-200 bg-white p-4">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-sm font-semibold">
                            {{ $detail->attendance?->date?->format('d M Y') ?? '-' }}
                        </p>

                        <p class="mt-1 text-sm text-slate-600">
                            {{ $detail->attendance?->teachingAssignment?->subject?->name ?? 'Pelajaran' }}
                        </p>

                        @if ($detail->attendance?->meeting_number)
                            <p class="mt-1 text-xs text-slate-500">
                                Pertemuan ke-{{ $detail->attendance->meeting_number }}
                            </p>
                        @endif
                    </div>

                    <span
                        class="shrink-0 rounded-full px-3 py-1 text-xs font-semibold {{ $statusClasses[$status] ?? 'bg-slate-100 text-slate-700' }}">
                        {{ $statusLabels[$status] ?? ucfirst($status) }}
                    </span>
                </div>

                @if ($detail->notes)
                    <p class="mt-3 border-t border-slate-100 pt-3 text-sm text-slate-500">
                        Catatan: {{ $detail->notes }}
                    </p>
                @endif
            </article>
        @empty
            <div class="rounded-xl border border-dashed border-slate-300 bg-white p-6 text-center">
                <p class="font-semibold">Belum ada catatan kehadiran</p>
                <p class="mt-2 text-sm text-slate-500">
                    Riwayat kehadiran anak akan tampil di sini setelah tersedia.
                </p>
            </div>
        @endforelse
    </section>
@endsection
