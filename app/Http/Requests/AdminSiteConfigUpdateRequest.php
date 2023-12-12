<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminSiteConfigUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:128'],
            'footerText' => ['required', 'string', 'max:128'],
            'alternateEmail' => ['email', 'max:256', 'nullable'],
            'securityTeamEmail' => ['required', 'email', 'max:256'],
        ];
    }
}



