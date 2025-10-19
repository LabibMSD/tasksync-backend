<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginAuthRequest;
use App\Http\Requests\Auth\RegisterAuthRequest;
use App\Http\Resources\AuthResource;
use App\Http\Responses\ApiResponse;
use App\Services\AuthService;
use Illuminate\Http\Request;

/**
 * @group Auth
 *
 * APIs for authentication
 */
class AuthController extends Controller
{
    private AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * @bodyParam password_confirmation string required The value and password must match. Example: |]|{+-
     */
    public function register(RegisterAuthRequest $request): ApiResponse
    {
        $validated = $request->validated();

        $result = $this->authService->register($validated);

        return ApiResponse::ok('Registered successfully', ['auth' => AuthResource::make($result)]);
    }

    public function login(LoginAuthRequest $request): ApiResponse
    {
        $validated = $request->validateUser();

        $result = $this->authService->login($validated);

        return ApiResponse::ok('Login successfully', ['auth' => AuthResource::make($result)]);
    }

    /**
     * @authenticated
     */
    public function logout(Request $request): ApiResponse
    {
        $result = $this->authService->logout($request->user());

        return ApiResponse::ok(
            'Logged out successfully',
            ['auth' => AuthResource::make($result)]
        );
    }
}
