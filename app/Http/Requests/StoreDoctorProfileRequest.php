<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDoctorProfileRequest extends FormRequest
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
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 're quired|string|max:255',
            'email' => 're quired|string|email|max:255|unique:users,email,',
            'password' => 'required|confirmed|string|min:8|max:20',
            'phone' => 're quired|string|max:255|unique:users,phone,',
            'birth_date' => 'required',
            'specialization' => 'required|string|max:255',
            'department_id' => 're quired|exists:departments,id',
            'image' => 'nullable|image|max:2048|mimes:jpg,jpeg,png',
            'fee' => 'required|numeric|min:30',
        ];
    }
}
