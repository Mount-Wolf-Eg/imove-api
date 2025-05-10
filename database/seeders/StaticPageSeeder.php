<?php

namespace Database\Seeders;

use App\Models\StaticPage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StaticPageSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement("SET foreign_key_checks=0");
        StaticPage::truncate();
        DB::statement("SET foreign_key_checks=1");

        StaticPage::create([
            'content' => json_encode(['en' => 'Terms And Conditions Patient', 'ar' => 'الشروط و الاحكام الخاصة بالمريض']),
            'page'    => 'terms_and_conditions_patient',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        StaticPage::create([
            'content' => json_encode(['en' => 'Terms And Conditions Doctor', 'ar' => 'الشروط و الاحكام الخاصة بالدكتور']),
            'page'    => 'terms_and_conditions_doctor',
            'created_at' => now(),
            'updated_at' => now(),
        ]);


    }
    
}
