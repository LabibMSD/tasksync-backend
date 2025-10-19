<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    private UserService $userService;
    private TokenService $tokenService;

    public function __construct(UserService $userService, TokenService $tokenService)
    {
        $this->userService = $userService;
        $this->tokenService = $tokenService;
    }

    public function register(array $data): array
    {
        $user = $this->userService->create($data);
        $token = $this->tokenService->generate($user, 'auth');

        return [
            'user' => $user,
            'token' => $token
        ];
    }

    public function login(User $user): array
    {
        $this->tokenService->revokeAll($user);

        $token = $this->tokenService->generate($user, 'auth');

        return [
            'user' => $user,
            'token' => $token
        ];
    }

    public function logout(User $user): array
    {
        $this->tokenService->revokeAll($user);

        return ['user' => $user];
    }

    public function currentUserId(): int
    {
        return Auth::id();
    }
}
