<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class numericOrNull implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if(empty($value)){ // empty
            return true;
        }

        return is_numeric($value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'قم بكتابة الرقم بشكل صحيح أو تركه فارغاً';
    }
}
