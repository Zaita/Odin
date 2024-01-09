<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminContentPillarUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:128', 'min:3'],
            'caption' => ['required', 'string', 'max:512', 'min:1'],
            'type' => ['required', 'string', 'max:40', 'min:1', Rule::in(['questionnaire', 'risk_questionnaire'])],
            'key_information' => ['required', 'string', 'max:1024', "min:2"],
            'risk_calculation' => ['required', 'string', 'max:40', Rule::in(['none', 'zaita_approx', 'highest_value'])],
        ];
    }
}


