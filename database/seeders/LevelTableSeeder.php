<?php

namespace Database\Seeders;

use App\Models\Admin\Level;
use Illuminate\Database\Seeder;

class LevelTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Level::create([
            'name_ar' => 'الصف الأول الإعدادي',
            'name_en' => 'prepratory_1'
        ]);

        Level::create([
            'name_ar' => 'الصف الثاني الإعدادي',
            'name_en' => 'prepratory_2'
        ]);

        Level::create([
            'name_ar' => 'الصف الثالث الإعدادي',
            'name_en' => 'prepratory_3'
        ]);

        Level::create([
            'name_ar' => 'الصف الأول الثانوي',
            'name_en' => 'secondary_1'
        ]);

        Level::create([
            'name_ar' => 'الصف الثاني الثانوي',
            'name_en' => 'secondary_2'
        ]);

        Level::create([
            'name_ar' => 'الصف الثالث الثانوي',
            'name_en' => 'secondary_3'
        ]);
    }
}
