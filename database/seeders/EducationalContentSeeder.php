<?php

namespace Database\Seeders;

use App\Models\Consultation;
use App\Models\EducationalContent;
use App\Models\MedicalSpeciality;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EducationalContentSeeder extends Seeder
{
    public function run()
    {

        $doctor = User::whereHas('doctor')->first();
        if (!$doctor) {
            $doctor = User::factory()->create(['is_doctor' => true]); 
        }

        $medicalSpeciality = MedicalSpeciality::first();
        if (!$medicalSpeciality) {
            $medicalSpeciality = MedicalSpeciality::create(['name' => 'General Medicine']);
        }

        $consultation = Consultation::find(569);
        if (!$consultation) {
            $consultation = Consultation::create([
                'id' => 569,
                'doctor_id' => $doctor->id,
                'patient_id' => User::factory()->create()->id,
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // educational_contents
        $contents = [
            [
                'author_id' => $doctor->id,
                'title' => ['en' => 'Understanding Diabetes', 'ar' => 'فهم مرض السكري'],
                'content' => ['en' => 'Content about diabetes management...', 'ar' => 'محتوى عن إدارة مرض السكري...'],
                'medical_speciality_id' => $medicalSpeciality->id,
                'publish_date' => now()->subDays(10),
                'views' => rand(50, 200),
                'shares' => rand(5, 20),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'author_id' => $doctor->id,
                'title' => ['en' => 'Heart Health Tips', 'ar' => 'نصائح لصحة القلب'],
                'content' => ['en' => 'Tips for maintaining heart health...', 'ar' => 'نصائح للحفاظ على صحة القلب...'],
                'medical_speciality_id' => $medicalSpeciality->id,
                'publish_date' => now()->subDays(5),
                'views' => rand(50, 200),
                'shares' => rand(5, 20),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'author_id' => $doctor->id,
                'title' => ['en' => 'Asthma Management', 'ar' => 'إدارة الربو'],
                'content' => ['en' => 'How to manage asthma effectively...', 'ar' => 'كيفية إدارة الربو بفعالية...'],
                'medical_speciality_id' => $medicalSpeciality->id,
                'publish_date' => now()->subDays(3),
                'views' => rand(50, 200),
                'shares' => rand(5, 20),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'author_id' => $doctor->id,
                'title' => ['en' => 'Nutrition Guide', 'ar' => 'دليل التغذية'],
                'content' => ['en' => 'A guide to balanced nutrition...', 'ar' => 'دليل للتغذية المتوازنة...'],
                'medical_speciality_id' => $medicalSpeciality->id,
                'publish_date' => now()->subDays(2),
                'views' => rand(50, 200),
                'shares' => rand(5, 20),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'author_id' => $doctor->id,
                'title' => ['en' => 'Mental Health Awareness', 'ar' => 'التوعية بالصحة النفسية'],
                'content' => ['en' => 'Understanding mental health issues...', 'ar' => 'فهم قضايا الصحة النفسية...'],
                'medical_speciality_id' => $medicalSpeciality->id,
                'publish_date' => now()->subDays(1),
                'views' => rand(50, 200),
                'shares' => rand(5, 20),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($contents as $content) {
            EducationalContent::create($content);
        }

 
    }
}