<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ActionFieldRequest extends FormRequest
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
            'label' => ['required', 'string', "min:3", "max:128"],
            'action_type' => ['required', Rule::in(["continue", "goto", "finish", "message"])],
            'goto_question_title' => ['string', 'max:256', 'nullable', 'required_if:action_type,goto'],
            'tasks.*' => ['array:label,value', 'nullable'],
        ];
    }
}



