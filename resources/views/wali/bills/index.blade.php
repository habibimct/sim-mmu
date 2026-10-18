@extends('layouts.wali')

@section('title', 'Tagihan')

@section('content')
    <header class="mb-5">
        <p class="text-xs text-slate-500">SIM-MMU · Portal Orang Tua</p>
        <h1 class="mt-2 text-xl font-bold">Tagihan dan Pembayaran</h1>
        <p class="mt-1 text-sm text-slate-600">
            Daftar tagihan {{ $student->name }}.
        </p>
    </header>

    <a href="{{ route('wali.dashboard') }}" class="mb-4 inline-flex items-center gap-2 text-sm font-medium text-emerald-700">
        ← Kembali ke Beranda
    </a>

    <section class="mb-4 rounded-xl border border-slate-200 bg-white p-4">
        <p class="text-xs text-slate-500">Nama anak</p>
        <h2 class="mt-1 font-semibold">{{ $student->name }}</h2>
    </section>


    {{-- Ringkasan tagihan --}}
    <section class="mb-5">
        <h2 class="mb-3 text-sm font-bold">Ringkasan Tagihan</h2>

        <div class="rounded-xl border border-slate-200 bg-white p-4">
            <div class="flex items-center justify-between gap-3">
                <p class="text-sm text-slate-500">Total tagihan</p>
                <p class="font-semibold text-slate-800">
                    Rp {{ number_format($totalBill, 0, ',', '.') }}
                </p>
            </div>

            <div class="mt-3 flex items-center justify-between gap-3">
                <p class="text-sm text-slate-500">Sudah dibayar</p>
                <p class="font-semibold text-emerald-700">
                    Rp {{ number_format($totalPaid, 0, ',', '.') }}
                </p>
            </div>

            <div class="mt-3 border-t border-slate-100 pt-3">
                <p class="text-sm text-slate-500">Total sisa tagihan</p>
                <p class="mt-1 text-2xl font-bold text-amber-800">
                    Rp {{ number_format($totalRemaining, 0, ',', '.') }}
                </p>
                <p class="mt-1 text-xs text-slate-500">
                    Jumlah yang masih perlu dibayar
                </p>
            </div>
        </div>
    </section>


    @forelse ($bills as $bill)
        @php
            $remaining = $bill->remaining_amount;
            $paid = $bill->paid_amount;
        @endphp

        <article class="mb-3 rounded-xl border border-slate-200 bg-white p-4">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h2 class="font-semibold">
                        {{ $bill->billType?->name ?? 'Tagihan' }}
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Periode: {{ $bill->period ?: '-' }}
                    </p>

                    @if ($bill->due_date)
                        <p class="mt-1 text-xs text-slate-500">
                            Jatuh tempo:
                            {{ $bill->due_date->format('d M Y') }}
                        </p>
                    @endif
                </div>

                @if ($remaining <= 0)
                    <span class="shrink-0 rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-800">
                        Lunas
                    </span>
                @elseif ($paid > 0)
                    <span class="shrink-0 rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-800">
                        Sebagian
                    </span>
                @else
                    <span class="shrink-0 rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-800">
                        Belum dibayar
                    </span>
                @endif
            </div>

            <div class="mt-4 space-y-2 border-t border-slate-100 pt-3 text-sm">
                <div class="flex justify-between gap-3">
                    <span class="text-slate-500">Jumlah tagihan</span>
                    <span class="font-medium">
                        Rp {{ number_format($bill->amount, 0, ',', '.') }}
                    </span>
                </div>

                <div class="flex justify-between gap-3">
                    <span class="text-slate-500">Sudah dibayar</span>
                    <span class="font-medium text-emerald-700">
                        Rp {{ number_format($paid, 0, ',', '.') }}
                    </span>
                </div>

                <div class="flex justify-between gap-3">
                    <span class="font-semibold">Sisa tagihan</span>
                    <span class="font-bold {{ $remaining > 0 ? 'text-amber-800' : 'text-emerald-700' }}">
                        Rp {{ number_format($remaining, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </article>
    @empty
        <div class="rounded-xl border border-dashed border-slate-300 bg-white p-6 text-center">
            <h2 class="font-semibold">Belum ada tagihan</h2>
            <p class="mt-2 text-sm text-slate-500">
                Belum ada tagihan yang tercatat untuk anak ini.
            </p>
        </div>
    @endforelse
@endsection
