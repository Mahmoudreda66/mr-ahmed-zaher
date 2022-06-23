<?php

namespace Database\Seeders;

use App\Models\Admin\Subject;
use Illuminate\Database\Seeder;

class SubjectTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Subject::create([
            'name_ar' => 'لغة العربية',
            'division' => null,
            'name_en' => 'arabic',
            'level' => 2
        ]);

        Subject::create([
            'name_ar' => 'رياضيات',
            'division' => null,
            'name_en' => 'maths',
            'level' => 2
        ]);

        Subject::create([
            'name_ar' => 'لغة الإنجليزية',
            'division' => null,
            'name_en' => 'english',
            'level' => 2
        ]);

        Subject::create([
            'name_ar' => 'علوم',
            'division' => null,
            'name_en' => 'scince',
            'level' => 0
        ]);

        Subject::create([
            'name_ar' => 'دراسات الإجتماعية',
            'division' => null,
            'name_en' => 'sociall_studies',
            'level' => 0
        ]);

        Subject::create([
            'name_ar' => 'كيمياء',
            'division' => 0,
            'name_en' => 'chemistry',
            'level' => 1
        ]);

        Subject::create([
            'name_ar' => 'فيزياء',
            'division' => 0,
            'name_en' => 'physics',
            'level' => 1
        ]);

        Subject::create([
            'name_ar' => 'أحياء',
            'division' => 0,
            'name_en' => 'biology',
            'level' => 1
        ]);

        Subject::create([
            'name_ar' => 'جيولوجيا',
            'division' => 0,
            'name_en' => 'geology',
            'level' => 1
        ]);

        Subject::create([
            'name_ar' => 'جغرافيا',
            'division' => 1,
            'name_en' => 'geography',
            'level' => 1
        ]);

        Subject::create([
            'name_ar' => 'تاريخ',
            'division' => 1,
            'name_en' => 'history',
            'level' => 1
        ]);

        Subject::create([
            'name_ar' => 'فلسفة',
            'division' => 1,
            'name_en' => 'philosophy',
            'level' => 1
        ]);

        Subject::create([
            'name_ar' => 'علم نفس',
            'division' => 1,
            'name_en' => 'psychology',
            'level' => 1
        ]);

        Subject::create([
            'name_ar' => 'لغة الفرنسية',
            'division' => null,
            'name_en' => 'french',
            'level' => 2
        ]);

        Subject::create([
            'name_ar' => 'لغة الألمانية',
            'division' => null,
            'name_en' => 'germany',
            'level' => 2
        ]);
    }
}
