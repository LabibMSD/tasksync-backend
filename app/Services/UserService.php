<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;

class UserService
{
    private UserRepository $userRepository;
    private TokenService $tokenService;

    public function __construct(UserRepository $userRepository, TokenService $tokenService)
    {
        $this->userRepository = $userRepository;
        $this->tokenService = $tokenService;
    }

    public function create(array $data): User
    {
        $data['password'] = Hash::make($data['password']);
        $data['role'] = "user";

        return $this->userRepository->create($data);
    }

    public function getAll(): Collection
    {
        return $this->userRepository->getAll();
    }

    public function update(User $user, array $data): ?User
    {
        return $this->userRepository->update($user, $data);
    }

    public function delete(User $user): bool
    {
        $this->tokenService->revokeAll($user);
        return $this->userRepository->delete($user);
    }
}
