<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    private UserRepository $userRepository;
    private TokenService $tokenService;

    public function __construct(UserRepository $userRepository, TokenService $tokenService)
    {
        $this->userRepository = $userRepository;
        $this->tokenService = $tokenService;
    }

    public function register(array $data): array
    {
        $data['password'] = Hash::make($data['password']);

        $user = $this->userRepository->create($data);
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
