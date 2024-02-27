<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TaskSaveRequest extends FormRequest
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
            'type' => ['required', Rule::in(["questionnaire", "risk_questionnaire", "security_risk_assessment", "control_validation_audit"])],
            'key_information' => ['required','string'],
            'lock_when_complete' => ['boolean', 'required'],
            'approval_required' => ['boolean', 'required'],
            'approval_group' => ['string', 'required'],
            'notification_group' => ['string', 'required'],
            'risk_calculation' => ['string', 'required'],
            'custom_risks' => ['boolean', 'required'],
            'time_to_complete' => ['string', 'nullable'],
            'time_to_review' => ['string', 'nullable'],
        ];
    }
}



