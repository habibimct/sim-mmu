<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Services\AiGradingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AiGradingController extends Controller
{
    public function index()
    {
        return view('guru.ai-grading.index');
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'class_name' => [
                'required',
                'string',
                'max:100',
            ],

            'subject_name' => [
                'required',
                'string',
                'max:150',
            ],

            'assessment_name' => [
                'required',
                'string',
                'max:200',
            ],

            'student_name' => [
                'required',
                'string',
                'max:150',
            ],

            'questions' => [
                'required',
                'array',
                'min:1',
            ],

            'questions.*.answer' => [
                'required',
                'string',
                'max:5000',
            ],

            'questions.*.weight' => [
                'required',
                'numeric',
                'min:0',
                'max:1000',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | Simpan sementara
        |--------------------------------------------------------------------------
        |
        | Tidak masuk database.
        | Data hanya berada di session selama proses koreksi.
        |
        */

        session([
            'ai_grading' => [
                'class_name' => $validated['class_name'],
                'subject_name' => $validated['subject_name'],
                'assessment_name' => $validated['assessment_name'],
                'student_name' => $validated['student_name'],
                'questions' => array_values($validated['questions']),
            ],
        ]);


        return redirect()
            ->route('guru.ai-grading.review')
            ->with('success', 'Data koreksi berhasil disiapkan.');
    }


    public function review()
    {
        $grading = session('ai_grading');


        if (! $grading) {
            return redirect()
                ->route('guru.ai-grading.index')
                ->with('error', 'Sesi koreksi tidak ditemukan.');
        }


        return view(
            'guru.ai-grading.review',
            compact('grading')
        );
    }

    public function process(
        Request $request,
        AiGradingService $aiGradingService
    ) {
        $grading = session('ai_grading');

        if (! $grading) {
            return redirect()
                ->route('guru.ai-grading.index')
                ->with('error', 'Sesi koreksi tidak ditemukan.');
        }

        $validated = $request->validate([
            'answer_images' => ['required', 'array', 'min:1'],

            'answer_images.*' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:10240',
            ],
        ]);

        $temporaryFiles = [];

        try {
            foreach ($validated['answer_images'] as $image) {
                $path = $image->store('tmp/ai-grading');

                $temporaryFiles[] = $path;
            }

            /*
         * Untuk sementara menggunakan simulator.
         *
         * Nanti bagian ini diganti dengan AI Vision sebenarnya.
         */
            $result = $aiGradingService->simulate(
                $grading,
                $temporaryFiles
            );

            session([
                'ai_grading.result' => $result,
            ]);

            return redirect()
                ->route('guru.ai-grading.result')
                ->with(
                    'success',
                    'Proses koreksi simulasi berhasil.'
                );
        } finally {

            /*
         * File jawaban bersifat temporary.
         * Setelah proses selesai, langsung hapus.
         */
            foreach ($temporaryFiles as $path) {
                if (Storage::exists($path)) {
                    Storage::delete($path);
                }
            }

            session()->forget('ai_grading.files');
        }
    }

    private function cleanupTemporaryFiles(): void
    {
        $files = session('ai_grading.files', []);

        foreach ($files as $path) {
            if (Storage::exists($path)) {
                Storage::delete($path);
            }
        }

        session()->forget('ai_grading.files');
    }

    public function result()
    {
        $grading = session('ai_grading');

        $result = session('ai_grading.result');

        if (! $grading || ! $result) {
            return redirect()
                ->route('guru.ai-grading.index')
                ->with(
                    'error',
                    'Hasil koreksi tidak ditemukan.'
                );
        }

        return view(
            'guru.ai-grading.result',
            compact('grading', 'result')
        );
    }

    public function saveReview(Request $request)
    {
        $grading = session('ai_grading');
        $result = session('ai_grading.result');

        if (! $grading || ! $result) {
            return redirect()
                ->route('guru.ai-grading.index')
                ->with('error', 'Data hasil koreksi tidak ditemukan.');
        }

        $validated = $request->validate([
            'results' => ['required', 'array', 'min:1'],

            'results.*.student_answer' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'results.*.final_score' => [
                'required',
                'numeric',
                'min:0',
            ],

            'results.*.teacher_note' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ]);

        $reviewedResults = [];

        foreach ($result['results'] as $index => $item) {
            $review = $validated['results'][$index] ?? [];

            $weight = (float) $item['weight'];

            $finalScore = (float) ($review['final_score'] ?? 0);

            // Nilai final tidak boleh melebihi bobot soal.
            $finalScore = min($finalScore, $weight);

            $reviewedResults[] = array_merge(
                $item,
                [
                    'student_answer' => $review['student_answer'] ?? '',
                    'final_score' => $finalScore,
                    'teacher_note' => $review['teacher_note'] ?? '',
                    'reviewed' => true,
                ]
            );
        }

        $result['results'] = $reviewedResults;
        $result['status'] = 'teacher_reviewed';

        session([
            'ai_grading.result' => $result,
        ]);

        return redirect()
            ->route('guru.ai-grading.result')
            ->with(
                'success',
                'Review guru berhasil disimpan.'
            );
    }

    public function reviewResult()
    {
        $grading = session('ai_grading');
        $result = session('ai_grading.result');

        if (! $grading || ! $result) {
            return redirect()
                ->route('guru.ai-grading.index')
                ->with('error', 'Data hasil koreksi tidak ditemukan.');
        }

        return view(
            'guru.ai-grading.review-result',
            compact('grading', 'result')
        );
    }
}
