<?php

namespace App\Http\Requests;

use App\Enums\BloodGroup;
use Illuminate\Foundation\Http\FormRequest;

class DoctorProfileRequest extends FormRequest
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
            'specialization' => 'nullable|string|max:255',
            'department_id' => 'nullable|exists:departments,id',
            'image' => 'nullable|image|max:2048|mimes:jpg,jpeg,png',
        ];
    }
}
