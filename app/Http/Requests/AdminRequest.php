<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        if (request()->method() == 'POST') {
            $password = 'required|confirmed|string|min:8|max:20'; // password_confirmation

        } elseif (request()->method() == 'PUT') {
            $password = 'nullable';
        }

        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'. request()->route()->admin_id,
            'password' => $password,
            'phone' => 'required|string|max:255|unique:users,phone,'. request()->route()->admin_id,
            'birth_date' => 'required',
            //premission
            'admin_management'=> 'nullable|boolean',
            'doctor_management'=> 'nullable|boolean',
            'salary_management'=> 'nullable|boolean',
            'absence_management'=> 'nullable|boolean',
        ];
    }
}
