<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StripeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'card_name' => 'required|string|min:9|max:50',
            'card_number' => 'required|string|size:16',
            'exp_month' => 'required|string|date_format:m',
            'exp_year' => 'required|string|date_format:Y',
            'cvc' => 'required|string|min:3|max:4',
        ];
    }
}
