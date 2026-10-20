@extends('layouts.wali')

@section('title', 'Beranda')

@section('content')

    {{-- Sapaan --}}
    <header class="flex items-center justify-between gap-2 mb-4">
        <div>
        <p class="text-xs text-slate-500">
            SIM-MMU · Portal Orang Tua
        </p>

        <h1 class="mt-2 text-xl font-bold tracking-tight">
            Assalamu'alaikum 👋
        </h1>

        <p class="mt-1 text-sm text-slate-600">
            Selamat datang Bpk/Ibu {{ $guardian->name }}. Mari pantau perkembangan anak Anda.
        </p>
        </div>

<form method="POST" action="{{ route('logout') }}"
    onsubmit="return confirm('Apakah Anda yakin ingin keluar dari akun?')">
    @csrf

    <button type="submit"
        class="flex w-full items-center justify-center gap-2 rounded-xl border border-red-200 bg-white px-4 py-3 text-sm font-semibold text-red-600 transition hover:bg-red-50">
        <svg xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 24 24" fill="none" stroke="currentColor"
            stroke-width="1.7" class="h-5 w-5" aria-hidden="true">
            <path d="M10 17l5-5-5-5" />
            <path d="M15 12H3" />
            <path d="M12 3h6a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-6" />
        </svg>

        Keluar
    </button>
</form>
    </header>

    {{-- Pilihan anak --}}
    <section class="mb-5" x-data="{
        selectedStudentId: {{ $students->first()?->id ?? 'null' }}
    }">
        @if ($students->isNotEmpty())
            <div class="rounded-xl border border-slate-200 bg-white p-3">
                <label for="selected-student" class="mb-2 block text-xs text-slate-500">
                    Anak yang sedang dilihat
                </label>

                <div class="flex items-center gap-3">
                    <div
                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-700">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.7" class="h-6 w-6" aria-hidden="true">
                            <circle cx="12" cy="8" r="4" />
                            <path d="M5 21v-2a7 7 0 0 1 14 0v2" />
                        </svg>
                    </div>

                    <div class="min-w-0 flex-1">
                        <select id="selected-student" x-model="selectedStudentId"
                            class="w-full border-0 bg-transparent p-0 pr-6 font-semibold text-slate-800 focus:ring-0">
                            @foreach ($students as $student)
                                <option value="{{ $student->id }}">
                                    {{ $student->name }}
                                </option>
                            @endforeach
                        </select>

                        @foreach ($students as $student)
                            <p x-show="selectedStudentId == '{{ $student->id }}'" class="mt-1 text-sm text-slate-500">
                                Kelas
                                {{ $student->studentAcademicYears->sortByDesc('id')->first()?->schoolClass?->name ?? '-' }}
                                · {{ $student->pivot->relationship }}
                            </p>
                        @endforeach
                    </div>
                </div>

                <p class="mt-2 text-xs text-slate-500">
                    Pilih nama untuk melihat informasi anak.
                </p>
            </div>
        @else
            <div class="rounded-xl border border-dashed border-slate-300 bg-white p-4 text-sm text-slate-600">
                Belum ada data anak yang terhubung dengan akun Anda.
            </div>
        @endif

        {{-- Ringkasan --}}
        <div class="mt-5">
            <h2 class="mb-3 text-sm font-bold">Ringkasan hari ini</h2>

            <div class="rounded-xl border border-slate-200 bg-white p-4">
                @if ($students->isNotEmpty())
                    <div>
                        <p class="text-sm text-slate-500">
                            Kehadiran anak hari ini
                        </p>

                        @foreach ($students as $student)
                            <div x-show="selectedStudentId == '{{ $student->id }}'" class="mt-3">
                                <p class="font-semibold">
                                    {{ $student->name }}
                                </p>

                                @php
                                    $summary = $attendanceSummary[$student->id] ?? [
                                        'present' => 0,
                                        'sick' => 0,
                                        'permission' => 0,
                                        'absent' => 0,
                                    ];
                                @endphp

                                <div class="mt-3 grid grid-cols-2 gap-2">
                                    <div class="rounded-lg bg-emerald-50 p-3">
                                        <p class="text-xs text-emerald-700">Hadir</p>
                                        <p class="mt-1 text-xl font-bold text-emerald-800">
                                            {{ $summary['present'] }}
                                        </p>
                                    </div>

                                    <div class="rounded-lg bg-sky-50 p-3">
                                        <p class="text-xs text-sky-700">Sakit</p>
                                        <p class="mt-1 text-xl font-bold text-sky-800">
                                            {{ $summary['sick'] }}
                                        </p>
                                    </div>

                                    <div class="rounded-lg bg-amber-50 p-3">
                                        <p class="text-xs text-amber-700">Izin</p>
                                        <p class="mt-1 text-xl font-bold text-amber-800">
                                            {{ $summary['permission'] }}
                                        </p>
                                    </div>

                                    <div class="rounded-lg bg-red-50 p-3">
                                        <p class="text-xs text-red-700">Alpa</p>
                                        <p class="mt-1 text-xl font-bold text-red-800">
                                            {{ $summary['absent'] }}
                                        </p>
                                    </div>
                                </div>

                                <a href="{{ route('wali.attendance.index', $student->id) }}"
                                    class="mt-4 flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4 py-3 text-sm font-semibold text-white hover:bg-emerald-700">
                                    Lihat Kehadiran
                                    <span aria-hidden="true">→</span>
                                </a>

                                <div class="mt-3 rounded-xl border border-amber-200 bg-amber-50 p-4">
                                    <div class="flex items-center justify-between gap-3">
                                        <div>
                                            <p class="text-sm font-medium text-amber-800">
                                                Sisa Tagihan
                                            </p>
                                            <p class="mt-1 text-xs text-amber-700">
                                                Total yang belum dibayar
                                            </p>
                                        </div>

                                        <div class="text-right">
                                            @if (($billSummary[$student->id] ?? 0) > 0)
                                                <p class="text-lg font-bold text-amber-900">
                                                    Rp
                                                    {{ number_format($billSummary[$student->id], 0, ',', '.') }}
                                                </p>
                                            @else
                                                <p class="text-sm font-semibold text-emerald-700">
                                                    Tidak ada tagihan
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-slate-500">
                        Belum ada data anak yang terhubung dengan akun Anda.
                    </p>
                @endif
            </div>
        </div>

        {{-- Akses cepat --}}
        <div class="mt-5">
            <h2 class="mb-3 text-sm font-bold">
                Apa yang ingin Anda lihat?
            </h2>

            <div class="grid grid-cols-2 gap-3">
                <a :href="selectedStudentId
                    ?
                    '{{ url('/wali/attendance') }}/' + selectedStudentId :
                    '#'"
                    class="rounded-xl border border-emerald-200 bg-white p-3 transition hover:bg-emerald-50">
                    <div class="mb-3 text-emerald-700">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.7" class="h-6 w-6" aria-hidden="true">
                            <rect x="3" y="5" width="18" height="16" rx="2" />
                            <path d="M16 3v4M8 3v4M3 11h18" />
                        </svg>
                    </div>

                    <h3 class="text-sm font-semibold">Kehadiran</h3>
                    <p class="mt-2 text-xs leading-relaxed text-slate-500">
                        Lihat riwayat hadir anak
                    </p>
                </a>

                <a :href="selectedStudentId
                    ?
                    '{{ url('/wali/bills') }}/' + selectedStudentId :
                    '#'"
                    class="rounded-xl border border-amber-200 bg-white p-3 transition hover:bg-amber-50">
                    <div class="mb-3 text-amber-700">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.7" class="h-6 w-6" aria-hidden="true">
                            <rect x="4" y="3" width="16" height="18" rx="2" />
                            <path d="M8 7h8M8 11h8M8 15h4" />
                        </svg>
                    </div>

                    <h3 class="text-sm font-semibold">Tagihan</h3>
                    <p class="mt-2 text-xs leading-relaxed text-slate-500">
                        Lihat tagihan dan pembayaran
                    </p>
                </a>
            </div>
        </div>
    </section>

@endsection
