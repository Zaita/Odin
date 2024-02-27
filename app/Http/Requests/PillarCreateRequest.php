<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PillarCreateRequest extends FormRequest
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
            'icon' => ['required', 'string', 'max:64', 'min:1'],
            'type' => ['required', 'string', 'max:40', 'min:1', Rule::in(['questionnaire', 'risk_questionnaire'])],
            'approval_flow' => ['required', 'string','max:128','min:3'],
            'key_information' => ['required', 'string', 'min:10'],
            'risk_calculation' => ['required', 'string', 'max:40', Rule::in(['none', 'zaita_approx', 'highest_value'])],
        ];
    }
}



