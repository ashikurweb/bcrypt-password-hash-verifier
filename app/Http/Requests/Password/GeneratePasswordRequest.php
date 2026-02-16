<?php

declare(strict_types=1);

namespace App\Http\Requests\Password;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GeneratePasswordRequest extends FormRequest
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
            'length' => ['required', 'integer', 'min:8', 'max:128'],
            'include_uppercase' => ['required', 'boolean'],
            'include_lowercase' => ['required', 'boolean'],
            'include_numbers' => ['required', 'boolean'],
            'include_special_characters' => ['required', 'boolean'],
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
            'length.required' => 'Password length is required.',
            'length.integer' => 'Password length must be a number.',
            'length.min' => 'Password length must be at least 8 characters.',
            'length.max' => 'Password length cannot exceed 128 characters.',
            'include_uppercase.required' => 'Uppercase option is required.',
            'include_uppercase.boolean' => 'Uppercase option must be true or false.',
            'include_lowercase.required' => 'Lowercase option is required.',
            'include_lowercase.boolean' => 'Lowercase option must be true or false.',
            'include_numbers.required' => 'Numbers option is required.',
            'include_numbers.boolean' => 'Numbers option must be true or false.',
            'include_special_characters.required' => 'Special characters option is required.',
            'include_special_characters.boolean' => 'Special characters option must be true or false.',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param \Illuminate\Validation\Validator $validator
     * @return void
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (
                !$this->boolean('include_uppercase') &&
                !$this->boolean('include_lowercase') &&
                !$this->boolean('include_numbers') &&
                !$this->boolean('include_special_characters')
            ) {
                $validator->errors()->add(
                    'character_types',
                    'At least one character type must be selected.'
                );
            }
        });
    }
}
