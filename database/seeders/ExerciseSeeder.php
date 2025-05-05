<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{Exercise, MedicalSpeciality};

class ExerciseSeeder extends Seeder
{
    public function run(): void
    {
        $exercises = [
            [
                'name' => ['en' => 'Push-Up', 'ar' => 'تمرين الضغط'],
                'brief' => ['en' => 'Upper body strength exercise', 'ar' => 'تمرين لتقوية الجزء العلوي من الجسم'],
                'description' => ['en' => 'Start in a plank position and lower your body.', 'ar' => 'ابدأ في وضعية البلانك ثم اخفض جسمك.'],
                'is_active' => true,
            ],
            [
                'name' => ['en' => 'Squat', 'ar' => 'سكوات'],
                'brief' => ['en' => 'Lower body strength exercise', 'ar' => 'تمرين لتقوية الجزء السفلي من الجسم'],
                'description' => ['en' => 'Stand with feet shoulder-width apart and squat down.', 'ar' => 'قف مع تباعد القدمين بعرض الكتفين واثنِ ركبتيك.'],
                'is_active' => true,
            ],
            [
                'name' => ['en' => 'Plank', 'ar' => 'بلانك'],
                'brief' => ['en' => 'Core stability exercise', 'ar' => 'تمرين لتثبيت عضلات البطن'],
                'description' => ['en' => 'Hold a push-up position for time.', 'ar' => 'ثبت في وضعية الضغط لوقت محدد.'],
                'is_active' => true,
            ],
        ];
        
        $specialities = MedicalSpeciality::latest()->take(10)->pluck('id');

        foreach ($exercises as $exercise) {
            $exercise = Exercise::create($exercise);
            $exercise->medicalSpecialities()->attach($specialities);
        }
    }
}
