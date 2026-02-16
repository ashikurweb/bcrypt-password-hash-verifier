<?php

declare(strict_types=1);

namespace App\Http\Requests\Password;

use Illuminate\Foundation\Http\FormRequest;

class HashPasswordRequest extends FormRequest
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
            'password' => ['required', 'string', 'min:1', 'max:255'],
            'rounds' => ['nullable', 'integer', 'min:4', 'max:20'],
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
            'password.max' => 'Password cannot exceed 255 characters.',
            'rounds.integer' => 'Rounds must be a number.',
            'rounds.min' => 'Rounds must be at least 4.',
            'rounds.max' => 'Rounds cannot exceed 20.',
        ];
    }
}
