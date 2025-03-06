<?php

namespace Database\Seeders;

use App\Models\ConsultationQuestion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ConsultationQuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $questions = [
            ['question' => ['en' => 'What is the nature of the pain you feel?', 'ar' => 'ما هو طبيعة الألم الذي تشعر به؟']],
            ['question' => ['en' => 'When did the pain start for the first time? Was there any incident or excessive effort?', 'ar' => 'متى بدأ الألم لأول مرة؟ وهل كان هناك أي حادث أو مجهود زائد؟']],
            ['question' => ['en' => 'Does the pain increase with a certain movement or at a specific time of the day?', 'ar' => 'هل يزداد الألم مع حركة معينة أو في وقت معين من اليوم؟']],
            ['question' => ['en' => 'Do you have any chronic diseases or take medications in the recent period?', 'ar' => 'هل لديك أي أمراض مزمنة أو تتناول أدوية في الفترة الأخيرة؟']],
            ['question' => ['en' => 'Have you tried any treatments or exercises to relieve the pain?', 'ar' => 'هل حاولت أي علاجات أو تمارين لتخفيف الألم؟']],
        ];

        foreach ($questions as $question) {
            ConsultationQuestion::create([
                'question' => $question['question'], // Storing name as JSON
            ]);
        }
    }
}
