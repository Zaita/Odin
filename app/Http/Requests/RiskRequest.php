<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RiskRequest extends FormRequest
{
  // protected $redirect = '/dashboard';
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'description' => ['required', 'string'],
        ];
    }
}



