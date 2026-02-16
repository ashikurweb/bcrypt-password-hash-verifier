<?php

declare(strict_types=1);

namespace App\Http\Requests\Password;

use Illuminate\Foundation\Http\FormRequest;

class VerifyPasswordRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'password' => ['required', 'string', 'min:1'],
            'hash' => ['required', 'string', 'size:60', 'regex:/^\$2[ayb]\$.{56}$/'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'password.required' => 'Password is required.',
            'password.string' => 'Password must be a string.',
            'password.min' => 'Password cannot be empty.',
            'hash.required' => 'Hash is required.',
            'hash.string' => 'Hash must be a string.',
            'hash.size' => 'The hash must be exactly 60 characters long.',
            'hash.regex' => 'The hash format is invalid (must be a valid bcrypt hash).',
        ];
    }
}
