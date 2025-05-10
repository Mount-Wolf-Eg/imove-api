<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MedicalEquipmentSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('medical_equipment')->insert([
            [
                'category_id' => 1,
                'name' => json_encode(['en' => 'Ventilator', 'ar' => 'جهاز تنفس']),
                'link' => 'https://example.com/ventilator',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'category_id' => 2,
                'name' => json_encode(['en' => 'Heart Monitor', 'ar' => 'جهاز مراقبة القلب']),
                'link' => 'https://example.com/heart-monitor',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
