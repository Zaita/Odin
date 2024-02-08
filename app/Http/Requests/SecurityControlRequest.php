<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SecurityControlRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
      return [
        'name' => ['required', 'string', 'max:128'],
        'description' => ['required', 'string', 'min:10'],
        'implementation_guidance' => ['nullable', 'string'],
        'implementation_evidence' => ['nullable', 'string'],
        'audit_guidance' => ['nullable', 'string'],
        'reference_standards'=> ['nullable', 'string'],
        'control_owner_name'=> ['nullable', 'string', 'max:128'],
        'control_owner_email'=> ['nullable', 'string', 'max:128'],
        'tags'=> ['nullable', 'string'],
    ];
    }
}
