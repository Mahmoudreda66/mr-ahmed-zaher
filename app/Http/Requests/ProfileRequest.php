<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
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
            'name' => 'required|max:191|min:3',
            'phone' => ['required', 'numeric', 'max:9999999999', 'min:10000000', Rule::unique('users', 'phone')->ignore(auth()->user())]
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'حقل الإسم مطلوب',
            'name.max' => 'يجب ألا يزيد الإسم عن 191 حرف',
            'name.min' => 'يجب ألا يقل الإسم عن 3 أحرف',
            'phone.required' => 'قم بكتابة رقم الهاتف',
            'phone.numeric' => 'يجب أن يتكون رقم الهاتف من أرقام فقط',
            'phone.max' => 'قم بكتابة رقم الهاتف بشكل صحيح',
            'phone.unique' => 'يجب ألا يتكرر رقم الهاتف مرتين',
            'phone.min' => 'قم بكتابة رقم الهاتف بشكل صحيح'
        ];
    }
}
