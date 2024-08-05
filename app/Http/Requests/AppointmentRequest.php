<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'doctor_id' => 'required|exists:users,id',
            // 'date' => 'required|string',
            'time' => 'required|date_format:H:i',
            'reason' => 'nullable|string|max:255',
            'day_name' => 'required|string',
        ];
    }
}
