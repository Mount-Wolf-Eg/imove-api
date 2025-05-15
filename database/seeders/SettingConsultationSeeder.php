<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class SettingConsultationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('setting_consultations')->insert([
            'minimum' => 50.00,
            'maximum' => 350.00,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
    }
}
