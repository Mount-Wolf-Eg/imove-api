<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoryMedicalEquipmentSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('category_medical_equipment')->insert([
            [
                'name' => json_encode(['en' => 'Respiratory Devices', 'ar' => 'أجهزة التنفس']),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => json_encode(['en' => 'Monitoring Equipment', 'ar' => 'أجهزة المراقبة']),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => json_encode(['en' => 'Diagnostic Tools', 'ar' => 'أدوات التشخيص']),
                'is_active' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
    
}
