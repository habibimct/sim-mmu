@extends('layouts.guru')

@section('content')
    <div class="space-y-6">

        {{-- Header --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h1 class="text-2xl font-bold text-gray-800">
                Koreksi Jawaban AI
            </h1>

            <p class="mt-1 text-sm text-gray-600">
                Bantu membaca, mencocokkan, dan memberikan saran nilai
                jawaban siswa dengan bantuan AI.
            </p>
        </div>


        {{-- Informasi --}}
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-5">
            <div class="flex gap-3">
                <div class="text-blue-600 text-xl">
                    ℹ️
                </div>

                <div>
                    <h2 class="font-semibold text-blue-800">
                        Cara kerja
                    </h2>

                    <p class="mt-1 text-sm text-blue-700">
                        Buat satu sesi koreksi untuk satu kelas.
                        Masukkan kunci jawaban, kemudian foto atau upload
                        lembar jawaban siswa untuk diperiksa oleh AI.
                    </p>
                </div>
            </div>
        </div>


        {{-- Mulai --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

                <div>
                    <h2 class="text-lg font-semibold text-gray-800">
                        Mulai Koreksi
                    </h2>

                    <p class="mt-1 text-sm text-gray-500">
                        Satu sesi menggunakan satu kunci jawaban untuk
                        seluruh siswa dalam kelas.
                    </p>
                </div>

                <button type="button" id="btn-start-correction"
                    class="inline-flex items-center justify-center gap-2
                           px-5 py-3 rounded-xl
                           bg-indigo-600 text-white font-semibold
                           hover:bg-indigo-700 transition">

                    <span class="text-lg">＋</span>

                    Mulai Koreksi Baru
                </button>

            </div>

        </div>


        {{-- Catatan --}}
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-5">

            <h2 class="font-semibold text-amber-800">
                Catatan
            </h2>

            <p class="mt-1 text-sm text-amber-700">
                AI hanya memberikan hasil pembacaan dan saran nilai.
                Nilai akhir tetap ditentukan oleh guru melalui proses review.
            </p>

        </div>

    </div>


    {{-- Modal --}}
    <div id="correction-modal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="correction-modal-title"
        role="dialog" aria-modal="true">

        <div class="flex min-h-screen items-center justify-center p-4">

            {{-- Overlay --}}
            <div id="modal-overlay" class="fixed inset-0 bg-black/40"></div>


            {{-- Modal Content --}}
            <div class="relative w-full max-w-3xl
                        bg-white rounded-2xl shadow-xl">

                {{-- Header --}}
                <div class="flex items-center justify-between
                            px-6 py-4 border-b">

                    <div>
                        <h2 id="correction-modal-title" class="text-lg font-bold text-gray-800">
                            Buat Koreksi Baru
                        </h2>

                        <p class="text-sm text-gray-500 mt-1">
                            Masukkan informasi ujian dan kunci jawaban.
                        </p>
                    </div>

                    <button type="button" id="btn-close-modal"
                        class="text-gray-400 hover:text-gray-700
                               text-2xl leading-none"
                        aria-label="Tutup">
                        ×
                    </button>

                </div>


                {{-- Body --}}
                <form id="correction-form" method="POST" action="{{ route('guru.ai-grading.store') }}">
                    @csrf

                    <div class="p-6 space-y-6">

                        {{-- Informasi ujian --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                            <div>
                                <label for="class_name" class="block text-sm font-medium text-gray-700">
                                    Kelas <span class="text-red-500">*</span>
                                </label>

                                <input type="text" id="class_name" name="class_name" placeholder="Contoh: VII A" required
                                    class="mt-1 block w-full rounded-xl border-gray-300
                                           focus:border-indigo-500 focus:ring-indigo-500">
                            </div>


                            <div>
                                <label for="subject_name" class="block text-sm font-medium text-gray-700">
                                    Mata Pelajaran <span class="text-red-500">*</span>
                                </label>

                                <input type="text" id="subject_name" name="subject_name" placeholder="Contoh: Matematika"
                                    required
                                    class="mt-1 block w-full rounded-xl border-gray-300
                                           focus:border-indigo-500 focus:ring-indigo-500">
                            </div>

                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label for="assessment_name" class="block text-sm font-medium text-gray-700">
                                    Nama Ujian / Tugas
                                    <span class="text-red-500">*</span>
                                </label>

                                <input type="text" id="assessment_name" name="assessment_name"
                                    placeholder="Contoh: Ulangan Harian Bab Pecahan" required
                                    class="mt-1 block w-full rounded-xl border-gray-300
                                       focus:border-indigo-500 focus:ring-indigo-500">

                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700">
                                    Nama Siswa
                                </label>

                                <p class="mt-1 text-xs text-gray-500">
                                    Masukkan nama siswa yang jawabannya akan dikoreksi.
                                </p>

                                <input type="text" name="student_name" maxlength="150" required
                                    placeholder="Contoh: Ahmad Fauzan"
                                    class="mt-2 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-800 focus:border-gray-500 focus:outline-none focus:ring-1 focus:ring-gray-500">
                            </div>
                        </div>

                        {{-- Kunci --}}
                        <div>

                            <div class="flex items-center justify-between mb-3">

                                <div>
                                    <h3 class="font-semibold text-gray-800">
                                        Kunci Jawaban
                                    </h3>

                                    <p class="text-sm text-gray-500">
                                        Masukkan soal/kunci dan bobot masing-masing.
                                    </p>
                                </div>

                                <button type="button" id="btn-add-question"
                                    class="px-3 py-2 rounded-lg
                                           border border-indigo-300
                                           text-indigo-600
                                           hover:bg-indigo-50
                                           text-sm font-medium">
                                    ＋ Tambah Soal
                                </button>

                            </div>


                            <div id="questions-container" class="space-y-4"></div>

                        </div>

                    </div>


                    {{-- Footer --}}
                    <div
                        class="flex flex-col-reverse sm:flex-row
                                sm:justify-end gap-3
                                px-6 py-4 border-t bg-gray-50
                                rounded-b-2xl">

                        <button type="button" id="btn-cancel-modal"
                            class="px-5 py-2.5 rounded-xl
                                   border border-gray-300
                                   text-gray-700
                                   hover:bg-gray-100">
                            Batal
                        </button>

                        <button type="submit"
                            class="px-5 py-2.5 rounded-xl
                                   bg-indigo-600 text-white
                                   font-semibold
                                   hover:bg-indigo-700">
                            Lanjut ke Pemeriksaan →
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const startButton = document.getElementById('btn-start-correction');
            const modal = document.getElementById('correction-modal');
            const overlay = document.getElementById('modal-overlay');
            const closeButton = document.getElementById('btn-close-modal');
            const cancelButton = document.getElementById('btn-cancel-modal');
            const addQuestionButton = document.getElementById('btn-add-question');
            const questionsContainer = document.getElementById('questions-container');
            const form = document.getElementById('correction-form');


            /*
            |--------------------------------------------------------------------------
            | Modal
            |--------------------------------------------------------------------------
            */

            function openModal() {
                modal.classList.remove('hidden');

                if (questionsContainer.children.length === 0) {
                    addQuestion();
                }
            }


            function closeModal() {
                modal.classList.add('hidden');
            }


            /*
            |--------------------------------------------------------------------------
            | Penomoran Soal
            |--------------------------------------------------------------------------
            */

            function renumberQuestions() {

                const questions =
                    questionsContainer.querySelectorAll('.question-item');

                questions.forEach((wrapper, index) => {

                    const number = index + 1;

                    wrapper.dataset.questionNumber = number;


                    // Judul
                    const title =
                        wrapper.querySelector('.question-title');

                    if (title) {
                        title.textContent = `Soal ${number}`;
                    }


                    // Kunci jawaban
                    const textarea =
                        wrapper.querySelector('textarea');

                    if (textarea) {
                        textarea.name =
                            `questions[${number}][answer]`;
                    }


                    // Bobot
                    const weight =
                        wrapper.querySelector('input[type="number"]');

                    if (weight) {
                        weight.name =
                            `questions[${number}][weight]`;
                    }

                });
            }


            /*
            |--------------------------------------------------------------------------
            | Tambah Soal
            |--------------------------------------------------------------------------
            */

            function addQuestion() {

                const number =
                    questionsContainer.children.length + 1;


                const wrapper =
                    document.createElement('div');


                wrapper.className =
                    'question-item rounded-xl border border-gray-200 p-4 bg-gray-50';


                wrapper.innerHTML = `
                <div class="flex items-center justify-between mb-3">

                    <h4 class="question-title font-semibold text-gray-800">
                        Soal ${number}
                    </h4>

                    <button
                        type="button"
                        class="remove-question text-sm text-red-600
                               hover:text-red-800"
                    >
                        Hapus
                    </button>

                </div>


                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

                    <div class="md:col-span-3">

                        <label class="block text-sm font-medium text-gray-700">
                            Kunci / Jawaban Acuan
                        </label>

                        <textarea
                            name="questions[${number}][answer]"
                            rows="3"
                            required
                            placeholder="Tuliskan kunci jawaban atau jawaban acuan..."
                            class="mt-1 block w-full rounded-xl border-gray-300
                                   focus:border-indigo-500 focus:ring-indigo-500"
                        ></textarea>

                    </div>


                    <div>

                        <label class="block text-sm font-medium text-gray-700">
                            Bobot
                        </label>

                        <input
                            type="number"
                            name="questions[${number}][weight]"
                            min="0"
                            step="0.5"
                            value="10"
                            required
                            class="mt-1 block w-full rounded-xl border-gray-300
                                   focus:border-indigo-500 focus:ring-indigo-500"
                        >

                    </div>

                </div>
            `;


                questionsContainer.appendChild(wrapper);


                /*
                |--------------------------------------------------------------------------
                | Tombol Hapus
                |--------------------------------------------------------------------------
                */

                wrapper
                    .querySelector('.remove-question')
                    .addEventListener('click', function() {

                        /*
                         * Minimal harus tersisa satu soal.
                         */
                        if (questionsContainer.children.length <= 1) {
                            return;
                        }


                        wrapper.remove();


                        /*
                         * Setelah dihapus,
                         * nomor soal disusun ulang.
                         */
                        renumberQuestions();

                    });


                /*
                |--------------------------------------------------------------------------
                | Pastikan nomor selalu rapi
                |--------------------------------------------------------------------------
                */

                renumberQuestions();
            }


            /*
            |--------------------------------------------------------------------------
            | Event
            |--------------------------------------------------------------------------
            */

            startButton.addEventListener('click', openModal);

            overlay.addEventListener('click', closeModal);

            closeButton.addEventListener('click', closeModal);

            cancelButton.addEventListener('click', closeModal);

            addQuestionButton.addEventListener('click', addQuestion);




        });
    </script>
@endsection
