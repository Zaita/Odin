<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmailMainRequest extends FormRequest
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
            'alternate_hostname_for_email' => ['string', 'max:128', 'nullable'],
            'email_from_address' => ['string', 'max:128', 'required'],
            'email_signature' => ['string', 'max:2048', 'required']
        ];
    }
}



