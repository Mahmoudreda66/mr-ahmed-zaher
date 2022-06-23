<?php

namespace App\Http\Requests;

use App\Rules\CurrentPasswordCheckRule;
use Illuminate\Foundation\Http\FormRequest;

class PasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'old_password' => ['required', 'min:6', new CurrentPasswordCheckRule],
            'new_password' => ['required', 'min:6', 'different:old_password']
        ];
    }

    public function messages()
    {
        return [
            'old_password.required' => 'قم بكتابة كلمة السر القديمة',
            'old_password.min' => 'يجب ألا تقل كلمة السر عن 6 أحرف',
            'new_password.required' => 'قم بكتابة كلمة السر الجديدة',
            'new_password.required' => 'يجب أن تتكون كلمة السر من 6 أحرف على الأقل',
            'new_password.different' => 'يجب إختيار كلمة سر مختلفة'
        ];
    }

    /**
     * Get the validation attributes that apply to the request.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'old_password' => __('current password'),
        ];
    }
}
