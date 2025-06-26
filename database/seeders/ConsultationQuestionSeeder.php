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
        $old_questions = [
            ['question' => ['en' => 'What is the nature of the pain you feel?', 'ar' => 'ما هو طبيعة الألم الذي تشعر به؟']],
            ['question' => ['en' => 'When did the pain start for the first time? Was there any incident or excessive effort?', 'ar' => 'متى بدأ الألم لأول مرة؟ وهل كان هناك أي حادث أو مجهود زائد؟']],
            ['question' => ['en' => 'Does the pain increase with a certain movement or at a specific time of the day?', 'ar' => 'هل يزداد الألم مع حركة معينة أو في وقت معين من اليوم؟']],
            ['question' => ['en' => 'Do you have any chronic diseases or take medications in the recent period?', 'ar' => 'هل لديك أي أمراض مزمنة أو تتناول أدوية في الفترة الأخيرة؟']],
            ['question' => ['en' => 'Have you tried any treatments or exercises to relieve the pain?', 'ar' => 'هل حاولت أي علاجات أو تمارين لتخفيف الألم؟']],
        ];

        $questions = [
            ['question' => ['en' => 'Describe your problem?', 'ar' => 'صف مشكلتك؟']],
            ['question' => ['en' => 'When did the problem start?', 'ar' => 'متى بدأت المشكلة؟']],
            ['question' => ['en' => 'How did the injury occur?', 'ar' => 'كيف حصلت الإصابة؟']],
            ['question' => ['en' => 'What increases the pain?', 'ar' => 'ما الذي يزيد الألم؟']],
            ['question' => ['en' => 'What relieves the pain?', 'ar' => 'ما الذي يخفف الألم؟']],
            ['question' => ['en' => 'What daily activities trigger the pain?', 'ar' => 'ما هي الأنشطة اليومية التي يعقبها الألم؟']],
            ['question' => ['en' => 'At what time of day do you feel the most pain?', 'ar' => 'في أي وقت من اليوم تشعر بالألم أكثر؟']],
            ['question' => ['en' => 'Do you feel anxious, sad, or stressed because of the pain?', 'ar' => 'هل تشعر بالقلق أو الحزن أو التوتر بسبب الألم؟']],
            ['question' => ['en' => 'Do you suffer from any chronic health problems?', 'ar' => 'هل تعاني من مشاكل صحية مزمنة؟']],
        ];


        foreach ($questions as $question) {
            ConsultationQuestion::create(['question' => $question['question'], 'is_active' => true]);
        }
    }
}
