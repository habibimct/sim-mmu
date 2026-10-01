@extends('layouts.guru')

@section('content')
    <div class="space-y-6">

        {{-- Header --}}
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    Pemeriksaan Koreksi
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Siapkan lembar jawaban siswa untuk diproses.
                </p>
            </div>

            <a href="{{ route('guru.ai-grading.index') }}"
                class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                ← Kembali
            </a>
        </div>

        {{-- Informasi Koreksi --}}
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <h2 class="text-lg font-semibold text-gray-800">
                Informasi Koreksi
            </h2>

            <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-3">

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

                <div>
                    <p class="text-xs font-medium uppercase text-gray-500">
                        Nama Siswa
                    </p>

                    <p class="mt-1 font-semibold text-gray-800">
                        {{ $grading['student_name'] }}
                    </p>
                </div>

            </div>
        </div>

        {{-- Kunci Jawaban --}}
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-800">
                        Kunci Jawaban
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Pastikan seluruh kunci jawaban sudah benar.
                    </p>
                </div>

                <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-600">
                    {{ count($grading['questions']) }} Soal
                </span>
            </div>

            <div class="mt-5 space-y-4">
                @foreach ($grading['questions'] as $index => $question)
                    <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="font-semibold text-gray-800">
                                    Soal {{ $index + 1 }}
                                </p>

                                <p class="mt-2 whitespace-pre-line text-sm text-gray-700">
                                    {{ $question['answer'] }}
                                </p>
                            </div>

                            <span
                                class="shrink-0 rounded-lg bg-white px-3 py-1 text-xs font-semibold text-gray-600 border border-gray-200">
                                Bobot {{ $question['weight'] }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Upload Jawaban --}}
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">

            <h2 class="text-lg font-semibold text-gray-800">
                Lembar Jawaban Siswa
            </h2>

            <p class="mt-1 text-sm text-gray-500">
                Ambil foto langsung menggunakan kamera atau pilih gambar dari perangkat.
            </p>

            <form id="answer-upload-form" method="POST" action="{{ route('guru.ai-grading.process') }}"
                enctype="multipart/form-data">
                @csrf

                <div class="mt-5">
                    <label for="answer-images"
                        class="flex cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed border-gray-300 bg-gray-50 px-6 py-10 text-center transition hover:border-gray-400 hover:bg-gray-100">
                        <div class="text-4xl">
                            📷
                        </div>

                        <p class="mt-3 font-semibold text-gray-700">
                            Foto / Pilih Lembar Jawaban
                        </p>

                        <p class="mt-1 text-sm text-gray-500">
                            Kamera HP atau file gambar dari perangkat
                        </p>

                        <span class="mt-4 inline-flex rounded-lg bg-gray-800 px-4 py-2 text-sm font-medium text-white">
                            Pilih Foto
                        </span>
                    </label>

                    <input id="answer-images" name="answer_images[]" type="file" accept="image/*" capture="environment"
                        multiple class="hidden">
                </div>

                {{-- Preview --}}
                <div id="preview-container" class="mt-6 hidden">
                    <div class="flex items-center justify-between">
                        <h3 class="font-semibold text-gray-800">
                            Preview Jawaban
                        </h3>

                        <span id="image-count" class="text-sm text-gray-500"></span>
                    </div>

                    <div id="preview-grid" class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3"></div>
                </div>

                {{-- Tombol proses --}}
                <div class="mt-6 flex justify-end">
                    <button id="process-images" type="submit" disabled
                        class="rounded-lg bg-gray-300 px-5 py-2.5 text-sm font-semibold text-gray-500 cursor-not-allowed">
                        Upload & Lanjutkan
                    </button>
                </div>

            </form>

        </div>

    </div>

    <script>
        const imageInput = document.getElementById('answer-images');
        const previewContainer = document.getElementById('preview-container');
        const previewGrid = document.getElementById('preview-grid');
        const imageCount = document.getElementById('image-count');
        const processButton = document.getElementById('process-images');

        imageInput.addEventListener('change', function() {
            previewGrid.innerHTML = '';

            const files = Array.from(this.files);

            if (files.length === 0) {
                previewContainer.classList.add('hidden');
                processButton.disabled = true;

                processButton.classList.remove(
                    'bg-gray-800',
                    'text-white',
                    'cursor-pointer'
                );

                processButton.classList.add(
                    'bg-gray-300',
                    'text-gray-500',
                    'cursor-not-allowed'
                );

                return;
            }

            previewContainer.classList.remove('hidden');

            imageCount.textContent =
                `${files.length} gambar dipilih`;

            files.forEach((file, index) => {
                const wrapper = document.createElement('div');

                wrapper.className =
                    'overflow-hidden rounded-xl border border-gray-200 bg-white';

                const image = document.createElement('img');

                image.className =
                    'h-64 w-full object-contain bg-gray-100';

                image.alt =
                    `Lembar jawaban ${index + 1}`;

                const info = document.createElement('div');

                info.className =
                    'border-t border-gray-200 px-4 py-3 text-sm font-medium text-gray-700';

                info.textContent =
                    `Halaman ${index + 1}`;

                wrapper.appendChild(image);
                wrapper.appendChild(info);

                previewGrid.appendChild(wrapper);

                const reader = new FileReader();

                reader.onload = function(event) {
                    image.src = event.target.result;
                };

                reader.readAsDataURL(file);
            });

            processButton.disabled = false;

            processButton.classList.remove(
                'bg-gray-300',
                'text-gray-500',
                'cursor-not-allowed'
            );

            processButton.classList.add(
                'bg-gray-800',
                'text-white',
                'cursor-pointer'
            );
        });
    </script>
@endsection
