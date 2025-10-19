<?php

namespace App\Services;

use App\Models\User;

class TokenService
{
    public function generate(User $user, string $tokenName): string
    {
        return $user->createToken($tokenName)->plainTextToken;
    }

    public function revokeAll(User $user): void
    {
        $user->tokens()->delete();
    }
}
