<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InputFieldRequest extends FormRequest
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
            'pillarId' => ['required', 'integer'],
            'questionId' => ['required', 'integer'],
            'inputId' => ['integer'],
            'label' => ['required', 'string', "min:3", "max:128"],
            'required' => ['boolean'],
            'input_type' => ['required', Rule::in(["text", "email", "textarea", "rich text editor", "dropdown", "date", "url", "radio button", "checkbox"])],
            'min_length' => ['integer', 'gte:0', 'nullable'],
            'max_length' => ['integer', 'gte:0', 'nullable'],
            'placeholder' => ['string', 'min:0', 'max:128', 'nullable'],
            'product_name' => ['boolean'],
            'business_owner' => ['boolean'],
            'ticket_url' => ['boolean'],
        ];
    }
}



