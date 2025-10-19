<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class UserRepository
{

    public function create(array $data): User
    {
        return User::create($data);
    }

    public function getAll(): Collection
    {
        return User::all();
    }

    public function update(User $user, array $data): ?User
    {
        $user->update($data);
        return $user;
    }

    public function delete(User $user): bool
    {
        return $user->delete();
    }
}
