<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class LoginRequest extends FormRequest
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
     * OWASP A03:2021 - Injection
     * A07:2021 - Identification and Authentication Failures
     */
    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'email:rfc,dns', // DNS validation to prevent fake emails
                'max:255',
            ],
            'password' => [
                'required',
                'string',
                'min:8', // OWASP recommended minimum
                'max:128', // Bcrypt limit
            ],
            'remember' => [
                'sometimes',
                'boolean',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'email.required' => 'Email address is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.max' => 'Email address is too long.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.max' => 'Password is too long.',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            back()->withErrors($validator)->withInput($this->except('password'))
        );
    }
}

