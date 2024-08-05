<?php

namespace App\Http\Requests;

use App\Enums\BloodGroup;
use Illuminate\Foundation\Http\FormRequest;

class PatientProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->user()->id;
        return [
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:users,email,' . $id,
            'phone' => 'nullable|string|max:255|unique:users,phone,' . $id,
            'birth_date' => 'nullable',
            'gender' => 'nullable|boolean',
            'address' => 'nullable|string|max:255',
            'blood_group' => 'nullable|string|in:' . BloodGroup::ALL,
            'image' => 'nullable|image|max:2048|mimes:jpg,jpeg,png',
        ];
    }
}
