<?php

namespace App\Services;

class AiGradingService
{
    /**
     * Simulasi proses koreksi jawaban.
     *
     * Belum menggunakan AI sungguhan.
     */
    public function simulate(array $grading, array $files): array
    {
        $results = [];

        foreach ($grading['questions'] as $index => $question) {
            $questionNumber = $index + 1;

            $results[] = [
                'question_number' => $questionNumber,

                // Sementara belum membaca tulisan tangan.
                'student_answer' => '[Simulasi jawaban siswa]',

                'answer_key' => $question['answer'],

                'weight' => (float) $question['weight'],

                // Nilai simulasi sementara.
                'suggested_score' => (float) $question['weight'],

                'confidence' => 'high',

                'status' => 'correct',

                'needs_review' => false,

                'feedback' => 'Jawaban sesuai dengan kunci jawaban.',
            ];
        }

        return [
            'status' => 'simulated',

            'files_processed' => count($files),

            'total_questions' => count($results),

            'results' => $results,
        ];
    }
}
