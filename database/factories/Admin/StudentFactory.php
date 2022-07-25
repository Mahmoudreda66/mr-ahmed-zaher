<?php

namespace Database\Factories\Admin;

use App\Models\Admin\Student;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class StudentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Student::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $code = rand(10, 99) . rand(10, 99) . rand(10, 99) . rand(10, 99);
        $exists = Student::where('code', $code)->select('code')->first();

        do {
            $code = rand(10, 99) . rand(10, 99) . rand(10, 99) . rand(10, 99);
        } while ($exists);

        $phone = '010' . rand(10000000, 99999999);
        return [
            'name' => $this->faker->name(),
            'level_id' => rand(1, 6),
            'mobile' => $phone,
            'gender' => rand(0, 1),
            'code' => $code,
            'user_id' => 1,
            'edu_type' => rand(0, 1),
            'division' => rand(0, 1)
        ];
    }
}
