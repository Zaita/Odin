<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SiteSettingsRequest extends FormRequest
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
            'odin_email' => ['email', 'max:256', 'nullable'],
            'security_team_email' => ['required', 'email', 'max:256'],
            'footer_text' => ['required', 'string', 'max:256'],
        ];
    }
}



