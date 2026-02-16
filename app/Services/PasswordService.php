<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Hash;
use InvalidArgumentException;

class PasswordService
{
    private const MIN_LENGTH = 8;
    private const MAX_LENGTH = 128;

    private const UPPERCASE_CHARS = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    private const LOWERCASE_CHARS = 'abcdefghijklmnopqrstuvwxyz';
    private const NUMBER_CHARS = '0123456789';
    private const SPECIAL_CHARS = '!@#$%^&*()_+-=[]{}|;:,.<>?';

    /**
     * Generate a random password based on configuration.
     *
     * @param int $length
     * @param bool $includeUppercase
     * @param bool $includeLowercase
     * @param bool $includeNumbers
     * @param bool $includeSpecial
     * @return string
     * @throws InvalidArgumentException
     */
    public function generate(
        int $length,
        bool $includeUppercase,
        bool $includeLowercase,
        bool $includeNumbers,
        bool $includeSpecial
    ): string {
        $this->validateGenerationConfig($length, $includeUppercase, $includeLowercase, $includeNumbers, $includeSpecial);

        $characterPool = $this->buildCharacterPool(
            $includeUppercase,
            $includeLowercase,
            $includeNumbers,
            $includeSpecial
        );

        $password = '';
        $poolLength = strlen($characterPool);

        for ($i = 0; $i < $length; $i++) {
            $password .= $characterPool[random_int(0, $poolLength - 1)];
        }

        return $password;
    }

    /**
     * Hash a password using bcrypt with optional rounds.
     *
     * @param string $password
     * @param int $rounds
     * @return string
     */
    public function hash(string $password, int $rounds = 10): string
    {
        return Hash::make($password, ['rounds' => $rounds]);
    }

    /**
     * Verify a password against a hash.
     *
     * @param string $password
     * @param string $hash
     * @return bool
     */
    public function verify(string $password, string $hash): bool
    {
        try {
            // Check if hash looks like a basic bcrypt hash to avoid unnecessary processing errors
            if (strlen($hash) !== 60 || !str_starts_with($hash, '$2')) {
                return false;
            }
            
            return Hash::check($password, $hash);
        } catch (\Throwable $e) {
            // If checking fails for any reason (e.g. malformed hash causing internal error), treat as invalid
            return false;
        }
    }

    /**
     * Calculate password strength score.
     *
     * @param string $password
     * @return array{score: int, strength: string}
     */
    public function calculateStrength(string $password): array
    {
        $length = strlen($password);
        $score = 0;

        $hasUppercase = preg_match('/[A-Z]/', $password);
        $hasLowercase = preg_match('/[a-z]/', $password);
        $hasNumbers = preg_match('/[0-9]/', $password);
        $hasSpecial = preg_match('/[^A-Za-z0-9]/', $password);

        $charTypes = (int)$hasUppercase + (int)$hasLowercase + (int)$hasNumbers + (int)$hasSpecial;

        // Base score from length
        if ($length < 8) {
            $score = min(20, $length * 2);
        } elseif ($length < 12) {
            $score = 30 + ($length - 8) * 5;
        } else {
            $score = 50 + min(30, ($length - 12) * 2);
        }

        // Bonus for character variety
        $score += $charTypes * 10;

        // Cap at 100
        $score = min(100, $score);

        $strength = $this->getStrengthLabel($score);

        return [
            'score' => $score,
            'strength' => $strength,
            'details' => [
                'length' => $length,
                'has_uppercase' => (bool)$hasUppercase,
                'has_lowercase' => (bool)$hasLowercase,
                'has_numbers' => (bool)$hasNumbers,
                'has_special' => (bool)$hasSpecial,
                'char_types' => $charTypes,
            ],
        ];
    }

    /**
     * Validate generation configuration.
     *
     * @throws InvalidArgumentException
     */
    private function validateGenerationConfig(
        int $length,
        bool $includeUppercase,
        bool $includeLowercase,
        bool $includeNumbers,
        bool $includeSpecial
    ): void {
        if ($length < self::MIN_LENGTH) {
            throw new InvalidArgumentException(
                "Password length must be at least {self::MIN_LENGTH} characters."
            );
        }

        if ($length > self::MAX_LENGTH) {
            throw new InvalidArgumentException(
                "Password length cannot exceed {self::MAX_LENGTH} characters."
            );
        }

        if (!$includeUppercase && !$includeLowercase && !$includeNumbers && !$includeSpecial) {
            throw new InvalidArgumentException(
                'At least one character type must be selected.'
            );
        }
    }

    /**
     * Build character pool based on options.
     */
    private function buildCharacterPool(
        bool $includeUppercase,
        bool $includeLowercase,
        bool $includeNumbers,
        bool $includeSpecial
    ): string {
        $pool = '';

        if ($includeUppercase) {
            $pool .= self::UPPERCASE_CHARS;
        }
        if ($includeLowercase) {
            $pool .= self::LOWERCASE_CHARS;
        }
        if ($includeNumbers) {
            $pool .= self::NUMBER_CHARS;
        }
        if ($includeSpecial) {
            $pool .= self::SPECIAL_CHARS;
        }

        return $pool;
    }

    /**
     * Get strength label based on score.
     */
    private function getStrengthLabel(int $score): string
    {
        return match (true) {
            $score <= 40 => 'weak',
            $score <= 70 => 'medium',
            default => 'strong',
        };
    }
}
