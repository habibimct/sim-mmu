
@extends('layouts.wali')

@section('title', 'Anak Saya')

@section('content')
    <header class="mb-5">
        <p class="text-xs text-slate-500">SIM-MMU · Portal Orang Tua</p>
        <h1 class="mt-2 text-xl font-bold tracking-tight">Anak Saya</h1>
        <p class="mt-1 text-sm text-slate-600">
            Informasi anak yang terhubung dengan akun Anda.
        </p>
    </header>

    @if ($students->isNotEmpty())
        <div class="mb-4">
            <p class="text-sm text-slate-500">
                Jumlah anak terhubung:
                <span class="font-semibold text-slate-800">
                    {{ $students->count() }} anak
                </span>
            </p>
        </div>

        @foreach ($students as $student)
            @php
                $studentAcademicYear = $student->studentAcademicYears
                    ->sortByDesc('id')
                    ->first();

                $className = $studentAcademicYear?->schoolClass?->name ?? '-';
                $relationship = $student->pivot->relationship ?: 'Wali';
            @endphp

            <article class="mb-3 rounded-xl border border-slate-200 bg-white p-4">
                <div class="flex items-start gap-3">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-700">
                        <svg xmlns="http://www.w3.org/2000/svg"
                             viewBox="0 0 24 24"
                             fill="none"
                             stroke="currentColor"
                             stroke-width="1.7"
                             class="h-6 w-6"
                             aria-hidden="true">
                            <circle cx="12" cy="8" r="4"/>
                            <path d="M4 21v-2a8 8 0 0 1 16 0v2"/>
                        </svg>
                    </div>

                    <div class="min-w-0 flex-1">
                        <h2 class="font-semibold text-slate-800">
                            {{ $student->name }}
                        </h2>

                        <p class="mt-1 text-sm text-slate-500">
                            Kelas {{ $className }}
                        </p>

                        <p class="mt-1 text-xs text-slate-500">
                            Hubungan: {{ $relationship }}
                        </p>
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-2 gap-2 border-t border-slate-100 pt-3">
                    <a href="{{ route('wali.attendance.index', $student->id) }}"
                       class="rounded-lg border border-emerald-200 px-3 py-2.5 text-center text-xs font-semibold text-emerald-700 transition hover:bg-emerald-50">
                        Lihat Kehadiran
                    </a>

                    <a href="{{ route('wali.bills.index', $student->id) }}"
                       class="rounded-lg border border-amber-200 px-3 py-2.5 text-center text-xs font-semibold text-amber-700 transition hover:bg-amber-50">
                        Lihat Tagihan
                    </a>
                </div>
            </article>
        @endforeach
    @else
        <div class="rounded-xl border border-dashed border-slate-300 bg-white p-6 text-center">
            <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-500">
                <svg xmlns="http://www.w3.org/2000/svg"
                     viewBox="0 0 24 24"
                     fill="none"
                     stroke="currentColor"
                     stroke-width="1.7"
                     class="h-6 w-6"
                     aria-hidden="true">
                    <circle cx="12" cy="8" r="4"/>
                    <path d="M4 21v-2a8 8 0 0 1 16 0v2"/>
                </svg>
            </div>

            <h2 class="font-semibold">Belum ada data anak</h2>
            <p class="mt-2 text-sm text-slate-500">
                Anak yang terhubung dengan akun Anda akan tampil di sini.
            </p>
        </div>
    @endif
@endsection
