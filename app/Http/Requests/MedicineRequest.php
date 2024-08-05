<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MedicineRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:30',
            'quantity' => 'required|numeric|min:0',
            'expiry_date' => 'required|string',
            'image' => 'nullable|image|max:2048|mimes:jpg,jpeg,png',
        ];
    }
}
