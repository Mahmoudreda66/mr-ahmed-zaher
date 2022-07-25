<?php

namespace Database\Seeders;

use App\Models\Admin\Settings;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Settings::create([
            'name' => 'expenses',
            'value' => json_encode([
                'prepratory_1' => '',
                'prepratory_2' => '',
                'prepratory_3' => '',
                'secondary_1' => '',
                'secondary_2' => '',
                'secondary_3' => '',
            ])
        ]);

        Settings::create([
            'name' => 'place_name',
            'value' => 'سمارت سنتر'
        ]);

        Settings::create([
            'name' => 'students_must_choose_teachers',
            'value' => 1
        ]);

        Settings::create([
            'name' => 'print_after_add_student',
            'value' => 1
        ]);

        Settings::create([
            'name' => 'center_phone1',
            'value' => '01093668025'
        ]);

        Settings::create([
            'name' => 'show_answers_after_exam_ends',
            'value' => 1
        ]);

        Settings::create([
            'name' => 'always_print_invoice_billing',
            'value' => 1
        ]);

        Settings::create([
            'name' => 'center_logo',
            'value' => ''
        ]);

        Settings::create([
            'name' => 'student_paper_text',
            'value' => ''
        ]);

        Settings::create([
            'name' => 'enable_students_online_application',
            'value' => 0
        ]);

        Settings::create([
            'name' => 'must_confirm_students_application',
            'value' => 1
        ]);
    }
}
