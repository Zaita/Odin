<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DSRATaskSaveRequest extends FormRequest
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
            'name' => ['required', 'string', 'min:3', 'max:128'],            
            'key_information' => ['required','string'],
            'initial_risk_impact' => ['string', 'required'],
            'security_catalogue' => ['string', 'required'],
            'custom_likelihoods' => ['boolean', 'required'],
            'custom_impacts' => ['boolean', 'required'],
            'time_to_complete' => ['string', 'nullable'],
            'time_to_review' => ['string', 'nullable'],
            'likelihoods' => ['text', 'optional'],
            'impacts' => ['text', 'optional'],
            'risk_matrix' => ['text', 'optional'],
        ];
    }
}



