<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ThemeRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            "login_bg_color" => ['required', 'string', 'max:7'],            
            "bg_color" => ['required', 'string', 'max:7'],            
            "text_color" => ['required', 'string', 'max:7'],            
            "header_color" => ['required', 'string', 'max:7'],            
            "header_text_color" => ['required', 'string', 'max:7'],            
            "subheader_color" => ['required', 'string', 'max:7'],            
            "subheader_text_color" => ['required', 'string', 'max:7'],            
            "breadcrumb_color" => ['required', 'string', 'max:7'],            
            "hyperlink_color" => ['required', 'string', 'max:7'],               
        ];
    }
}



