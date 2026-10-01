@extends('layouts.guru')

@section('content')
    <div class="space-y-6">

        {{-- Header --}}
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                Hasil Koreksi
            </h1>

            <p class="mt-1 text-sm text-gray-500">
                Hasil ini masih berupa simulasi dan belum menggunakan AI sebenarnya.
            </p>
        </div>

        {{-- Informasi --}}
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">

                <div>
                    <p class="text-xs font-medium uppercase text-gray-500">
                        Kelas
                    </p>

                    <p class="mt-1 font-semibold text-gray-800">
                        {{ $grading['class_name'] }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-medium uppercase text-gray-500">
                        Mata Pelajaran
                    </p>

                    <p class="mt-1 font-semibold text-gray-800">
                        {{ $grading['subject_name'] }}
                    </p>
                </div>

                <div>
                    <p class="text-xs font-medium uppercase text-gray-500">
                        Ujian / Tugas
                    </p>

                    <p class="mt-1 font-semibold text-gray-800">
                        {{ $grading['assessment_name'] }}
                    </p>
                </div>

            </div>

        </div>

        {{-- Status --}}
        <div class="rounded-xl border border-amber-200 bg-amber-50 p-4">
            <p class="text-sm font-medium text-amber-800">
                ⚠️ Mode Simulasi
            </p>

            <p class="mt-1 text-sm text-amber-700">
                Jawaban siswa belum dibaca oleh AI. Data di bawah hanya digunakan
                untuk menguji alur pemeriksaan.
            </p>
        </div>

        {{-- Hasil --}}
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">

            <div class="border-b border-gray-200 px-5 py-4">
                <h2 class="font-semibold text-gray-800">
                    Detail Koreksi
                </h2>
            </div>

            <div class="divide-y divide-gray-200">

                @foreach ($result['results'] as $item)
                    <div class="p-5">

                        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">

                            <div class="flex-1">

                                <p class="font-semibold text-gray-800">
                                    Soal {{ $item['question_number'] }}
                                </p>

                                <div class="mt-3 space-y-3">

                                    <div>
                                        <p class="text-xs font-medium uppercase text-gray-500">
                                            Jawaban Siswa
                                        </p>

                                        <p class="mt-1 text-sm text-gray-700">
                                            {{ $item['student_answer'] }}
                                        </p>
                                    </div>

                                    <div>
                                        <p class="text-xs font-medium uppercase text-gray-500">
                                            Kunci Jawaban
                                        </p>

                                        <p class="mt-1 text-sm text-gray-700 whitespace-pre-line">
                                            {{ $item['answer_key'] }}
                                        </p>
                                    </div>

                                    <div>
                                        <p class="text-xs font-medium uppercase text-gray-500">
                                            Catatan
                                        </p>

                                        <p class="mt-1 text-sm text-gray-600">
                                            {{ $item['feedback'] }}
                                        </p>
                                    </div>

                                </div>

                            </div>

                            <div class="flex gap-3">

                                <div class="rounded-xl bg-gray-50 px-4 py-3 text-center border border-gray-200">
                                    <p class="text-xs text-gray-500">
                                        Usulan AI
                                    </p>

                                    <p class="mt-1 text-xl font-bold text-gray-800">
                                        {{ $item['suggested_score'] }}
                                    </p>

                                    <p class="text-xs text-gray-500">
                                        / {{ $item['weight'] }}
                                    </p>
                                </div>

                                <div class="rounded-xl bg-gray-50 px-4 py-3 text-center border border-gray-200">
                                    <p class="text-xs text-gray-500">
                                        Nilai Final Guru
                                    </p>

                                    <p class="mt-1 text-xl font-bold text-gray-800">
                                        {{ $item['final_score'] ?? $item['suggested_score'] }}
                                    </p>

                                    <p class="text-xs text-gray-500">
                                        / {{ $item['weight'] }}
                                    </p>
                                </div>

                            </div>

                        </div>

                    </div>
                @endforeach

            </div>

        </div>

        {{-- Tombol --}}
        <div class="flex flex-col gap-3 sm:flex-row sm:justify-between">

            <a href="{{ route('guru.ai-grading.review') }}"
                class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50">
                ← Kembali
            </a>

            <a href="{{ route('guru.ai-grading.review-result') }}"
                class="rounded-lg bg-gray-800 px-5 py-2.5 text-center text-sm font-semibold text-white hover:bg-gray-700">
                Review Hasil
            </a>

        </div>

    </div>
@endsection
