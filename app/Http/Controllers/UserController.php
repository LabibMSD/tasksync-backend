<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Http\Responses\ApiResponse;
use App\Models\User;
use App\Services\UserService;

/**
 * @group Users
 * 
 * APIs for users
 * 
 * @authenticated
 */
class UserController extends Controller
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index(): ApiResponse
    {
        $users = $this->userService->getAll();

        if ($users->isEmpty()) {
            return ApiResponse::notFound(
                'No users found',
                ['users' => []]
            );
        }

        return ApiResponse::ok('Users found', ['users' => UserResource::collection($users)]);
    }

    public function store(StoreUserRequest $request): ApiResponse
    {
        $validated = $request->validated();

        $user = $this->userService->create($validated);
        return ApiResponse::ok('User created', ['user' => $user->toResource()]);
    }

    public function show(User $user): ApiResponse
    {
        return ApiResponse::ok('User found', ['user' => $user->toResource()]);
    }

    public function update(UpdateUserRequest $request, User $user): ApiResponse
    {
        $validated = $request->validated();

        $user = $this->userService->update($user, $validated);

        return ApiResponse::ok('User updated', ['user' => $user->toResource()]);
    }

    public function destroy(User $user): ApiResponse
    {
        $this->userService->delete($user);

        return ApiResponse::ok('User deleted', ['user' => $user->toResource()]);
    }
}
