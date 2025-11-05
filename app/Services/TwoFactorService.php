<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;

class TwoFactorService
{
    /**
     * Generate a 6-digit verification code
     */
    public function generateCode(): string
    {
        return str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Generate backup codes for user
     */
    public function generateBackupCodes(int $count = 8): array
    {
        $codes = [];
        for ($i = 0; $i < $count; $i++) {
            $codes[] = strtoupper(bin2hex(random_bytes(4)));
        }
        return $codes;
    }

    /**
     * Store code in cache with expiration
     */
    public function storeCode(User $user, string $code): void
    {
        Cache::put("2fa_code_{$user->id}", $code, now()->addMinutes(10));
    }

    /**
     * Verify code for user
     */
    public function verifyCode(User $user, string $code): bool
    {
        $cachedCode = Cache::get("2fa_code_{$user->id}");
        
        if (!$cachedCode) {
            return false;
        }

        // Check main code
        if ($cachedCode === $code) {
            Cache::forget("2fa_code_{$user->id}");
            return true;
        }

        // Check backup codes
        $backupCodes = $user->two_factor_backup_codes ?? [];
        if (in_array($code, $backupCodes)) {
            // Remove used backup code
            $backupCodes = array_values(array_diff($backupCodes, [$code]));
            $user->update(['two_factor_backup_codes' => $backupCodes]);
            return true;
        }

        return false;
    }

    /**
     * Enable 2FA for user
     */
    public function enableTwoFactor(User $user): array
    {
        $backupCodes = $this->generateBackupCodes();
        
        $user->update([
            'two_factor_enabled' => true,
            'two_factor_secret' => bin2hex(random_bytes(16)),
            'two_factor_backup_codes' => $backupCodes,
            'two_factor_verified_at' => now(),
        ]);

        return $backupCodes;
    }

    /**
     * Disable 2FA for user
     */
    public function disableTwoFactor(User $user): void
    {
        $user->update([
            'two_factor_enabled' => false,
            'two_factor_secret' => null,
            'two_factor_backup_codes' => null,
            'two_factor_verified_at' => null,
        ]);
    }
}

