<?php

namespace App\Support;

class PhoneNumber
{
    public static function normalize(?string $phone): ?string
    {
        if ($phone === null) {
            return null;
        }

        $trimmed = trim($phone);

        if ($trimmed === '') {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $trimmed) ?? '';

        if ($digits === '') {
            return null;
        }

        if (str_starts_with($digits, '0')) {
            $digits = '255' . substr($digits, 1);
        }

        if (!str_starts_with($digits, '255') && strlen($digits) === 9) {
            $digits = '255' . $digits;
        }

        if (!str_starts_with($digits, '255') && str_starts_with($digits, '00')) {
            $digits = ltrim(substr($digits, 2), '0');
        }

        return '+' . $digits;
    }

    public static function isValid(?string $phone): bool
    {
        $normalized = self::normalize($phone);

        if ($normalized === null) {
            return false;
        }

        return (bool) preg_match('/^\+[1-9]\d{9,14}$/', $normalized);
    }
}

