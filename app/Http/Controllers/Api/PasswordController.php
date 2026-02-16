<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Password\GeneratePasswordRequest;
use App\Http\Requests\Password\HashPasswordRequest;
use App\Http\Requests\Password\VerifyPasswordRequest;
use App\Services\PasswordService;
use Illuminate\Http\JsonResponse;
use InvalidArgumentException;
use Throwable;

class PasswordController extends Controller
{
    /**
     * Controller constructor with dependency injection.
     */
    public function __construct(
        private readonly PasswordService $passwordService
    ) {
    }

    /**
     * Generate a random password.
     */
    public function generate(GeneratePasswordRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();

            $password = $this->passwordService->generate(
                length: $validated['length'],
                includeUppercase: $validated['include_uppercase'],
                includeLowercase: $validated['include_lowercase'],
                includeNumbers: $validated['include_numbers'],
                includeSpecial: $validated['include_special_characters']
            );

            $strength = $this->passwordService->calculateStrength($password);

            return ApiResponse::success(
                data: [
                    'password' => $password,
                    'strength' => $strength,
                    'config' => [
                        'length' => $validated['length'],
                        'include_uppercase' => $validated['include_uppercase'],
                        'include_lowercase' => $validated['include_lowercase'],
                        'include_numbers' => $validated['include_numbers'],
                        'include_special_characters' => $validated['include_special_characters'],
                    ],
                ],
                message: 'Password generated successfully.'
            );
        } catch (InvalidArgumentException $e) {
            return ApiResponse::error(
                message: $e->getMessage(),
                code: 422
            );
        } catch (Throwable $e) {
            return ApiResponse::error(
                message: 'Failed to generate password. Please try again.',
                code: 500
            );
        }
    }

    /**
     * Hash a password.
     */
    public function hash(HashPasswordRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();

            $rounds = $validated['rounds'] ?? 10;
            $hashedPassword = $this->passwordService->hash($validated['password'], $rounds);

            return ApiResponse::success(
                data: [
                    'hashed_password' => $hashedPassword,
                    'algorithm' => 'bcrypt',
                    'rounds' => $rounds,
                ],
                message: 'Password hashed successfully.'
            );
        } catch (Throwable $e) {
            return ApiResponse::error(
                message: 'Failed to hash password. Please try again.',
                code: 500
            );
        }
    }

    /**
     * Verify a password against a hash.
     */
    public function verify(VerifyPasswordRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();

            $isValid = $this->passwordService->verify(
                password: $validated['password'],
                hash: $validated['hash']
            );

            return ApiResponse::success(
                data: [
                    'is_valid' => $isValid,
                    'password_matched' => $isValid,
                ],
                message: $isValid ? 'Password verification successful.' : 'Password does not match.'
            );
        } catch (Throwable $e) {
            return ApiResponse::error(
                message: 'Failed to verify password. Please try again.',
                code: 500
            );
        }
    }
}
