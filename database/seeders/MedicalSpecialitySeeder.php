<?php

namespace Database\Seeders;

use App\Models\MedicalSpeciality;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MedicalSpecialitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $specialities = [
            // Front Positions
            ['name' => ['en' => 'Head', 'ar' => 'الرأس'], 'position' => 'front'],
            ['name' => ['en' => 'Front Neck', 'ar' => 'الرقبة الأمامية'], 'position' => 'front'],
            ['name' => ['en' => 'Front Shoulder', 'ar' => 'الكتف الأمامي'], 'position' => 'front'],
            ['name' => ['en' => 'Chest', 'ar' => 'الصدر'], 'position' => 'front'],
            ['name' => ['en' => 'Front Arm', 'ar' => 'الذراع الأمامي'], 'position' => 'front'],
            ['name' => ['en' => 'The Arm', 'ar' => 'الذراع'], 'position' => 'front'],
            ['name' => ['en' => 'Elbow', 'ar' => 'المرفق'], 'position' => 'front'],
            ['name' => ['en' => 'Wrist and Hand', 'ar' => 'الرسغ واليد'], 'position' => 'front'],
            ['name' => ['en' => 'Belly', 'ar' => 'البطن'], 'position' => 'front'],
            ['name' => ['en' => 'Anterior Thigh', 'ar' => 'الفخذ الأمامي'], 'position' => 'front'],
            ['name' => ['en' => 'Front Leg', 'ar' => 'الساق الأمامية'], 'position' => 'front'],
            ['name' => ['en' => 'The Slap', 'ar' => 'الصفعة'], 'position' => 'front'],
            ['name' => ['en' => 'Knee', 'ar' => 'الركبة'], 'position' => 'front'],
            ['name' => ['en' => 'Ankle and Foot', 'ar' => 'الكاحل والقدم'], 'position' => 'front'],

            // Back Positions
            ['name' => ['en' => 'Back Neck', 'ar' => 'الرقبة الخلفية'], 'position' => 'back'],
            ['name' => ['en' => 'Back Shoulder', 'ar' => 'الكتف الخلفي'], 'position' => 'back'],
            ['name' => ['en' => 'Shoulder Blade', 'ar' => 'لوح الكتف'], 'position' => 'back'],
            ['name' => ['en' => 'Ribs', 'ar' => 'الأضلاع'], 'position' => 'back'],
            ['name' => ['en' => 'Rear Arm', 'ar' => 'الذراع الخلفي'], 'position' => 'back'],
            ['name' => ['en' => 'Lower Back', 'ar' => 'أسفل الظهر'], 'position' => 'back'],
            ['name' => ['en' => 'Duck Leg', 'ar' => 'رجل البطة'], 'position' => 'back'],
            ['name' => ['en' => 'Hind Leg', 'ar' => 'الرجل الخلفية'], 'position' => 'back'],
            ['name' => ['en' => 'Thigh', 'ar' => 'الفخذ'], 'position' => 'back'],
        ];

        foreach ($specialities as $speciality) {
            MedicalSpeciality::create([
                'name' => $speciality['name'], // Storing name as JSON
                'position' => $speciality['position'],
            ]);
        }
    }
}
