@extends('layouts.guru')

@section('content')

<div class="space-y-6">

    {{-- Header --}}
    <div>
        <h1 class="text-2xl font-bold text-gray-800">
            Review Hasil Koreksi
        </h1>

        <p class="mt-1 text-sm text-gray-500">
            Periksa hasil koreksi sebelum dianggap sebagai nilai final.
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

    {{-- Form Review --}}
    <form
        method="POST"
        action="{{ route('guru.ai-grading.save-review') }}"
        class="space-y-5"
    >

        @csrf

        @foreach ($result['results'] as $index => $item)

            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">

                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

                    <h2 class="text-lg font-semibold text-gray-800">
                        Soal {{ $item['question_number'] }}
                    </h2>

                    @if ($item['needs_review'] || $item['confidence'] !== 'high')

                        <span class="inline-flex w-fit rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">
                            ⚠️ Perlu Review
                        </span>

                    @else

                        <span class="inline-flex w-fit rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                            ✓ Confidence Tinggi
                        </span>

                    @endif

                </div>

                <div class="mt-5 grid grid-cols-1 gap-5 lg:grid-cols-2">

                    {{-- Jawaban siswa --}}
                    <div>

                        <label class="text-sm font-semibold text-gray-700">
                            Jawaban Siswa
                        </label>

                        <textarea
                            name="results[{{ $index }}][student_answer]"
                            rows="5"
                            class="mt-2 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-800 focus:border-gray-500 focus:outline-none focus:ring-1 focus:ring-gray-500"
                        >{{ $item['student_answer'] }}</textarea>

                        <p class="mt-1 text-xs text-gray-500">
                            Guru dapat memperbaiki hasil pembacaan jika diperlukan.
                        </p>

                    </div>

                    {{-- Kunci --}}
                    <div>

                        <label class="text-sm font-semibold text-gray-700">
                            Kunci Jawaban
                        </label>

                        <div class="mt-2 min-h-[120px] rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-700 whitespace-pre-line">
                            {{ $item['answer_key'] }}
                        </div>

                    </div>

                </div>

                {{-- AI info --}}
                <div class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-3">

                    <div class="rounded-lg bg-gray-50 p-4 border border-gray-200">

                        <p class="text-xs text-gray-500">
                            Usulan AI
                        </p>

                        <p class="mt-1 text-xl font-bold text-gray-800">
                            {{ $item['suggested_score'] }}
                            <span class="text-sm font-normal text-gray-500">
                                / {{ $item['weight'] }}
                            </span>
                        </p>

                    </div>

                    <div class="rounded-lg bg-gray-50 p-4 border border-gray-200">

                        <p class="text-xs text-gray-500">
                            Confidence
                        </p>

                        <p class="mt-1 font-semibold text-gray-800">
                            {{ ucfirst($item['confidence']) }}
                        </p>

                    </div>

                    <div class="rounded-lg bg-gray-50 p-4 border border-gray-200">

                        <p class="text-xs text-gray-500">
                            Catatan AI
                        </p>

                        <p class="mt-1 text-sm text-gray-700">
                            {{ $item['feedback'] }}
                        </p>

                    </div>

                </div>

                {{-- Nilai guru --}}
                <div class="mt-5">

                    <label
                        for="final-score-{{ $index }}"
                        class="text-sm font-semibold text-gray-700"
                    >
                        Nilai Final Guru
                    </label>

                    <div class="mt-2 flex items-center gap-3">

                        <input
                            id="final-score-{{ $index }}"
                            type="number"
                            name="results[{{ $index }}][final_score]"
                            value="{{ $item['final_score'] ?? $item['suggested_score'] }}"
                            min="0"
                            max="{{ $item['weight'] }}"
                            step="0.01"
                            required
                            class="w-32 rounded-lg border border-gray-300 px-3 py-2 text-sm font-semibold text-gray-800 focus:border-gray-500 focus:outline-none focus:ring-1 focus:ring-gray-500"
                        >

                        <span class="text-sm text-gray-500">
                            dari {{ $item['weight'] }}
                        </span>

                    </div>

                </div>

                {{-- Catatan guru --}}
                <div class="mt-5">

                    <label
                        for="teacher-note-{{ $index }}"
                        class="text-sm font-semibold text-gray-700"
                    >
                        Catatan Guru
                    </label>

                    <textarea
                        id="teacher-note-{{ $index }}"
                        name="results[{{ $index }}][teacher_note]"
                        rows="3"
                        placeholder="Catatan jika diperlukan..."
                        class="mt-2 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-800 focus:border-gray-500 focus:outline-none focus:ring-1 focus:ring-gray-500"
                    >{{ $item['teacher_note'] ?? '' }}</textarea>

                </div>

            </div>

        @endforeach

        {{-- Tombol --}}
        <div class="flex flex-col gap-3 sm:flex-row sm:justify-between">

            <a
                href="{{ route('guru.ai-grading.result') }}"
                class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50"
            >
                ← Kembali
            </a>

            <button
                type="submit"
                class="rounded-lg bg-gray-800 px-5 py-2.5 text-sm font-semibold text-white hover:bg-gray-700"
            >
                Simpan Review Guru
            </button>

        </div>

    </form>

</div>

@endsection
