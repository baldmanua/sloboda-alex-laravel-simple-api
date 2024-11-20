<?php

namespace App\Http\Controllers\Auth;

use App\DTOs\UserDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct(
        protected UserService $userService
    ) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $dto = new UserDTO(
            name: $request->input('name'),
            email: $request->input('email'),
            hashed_password: $request->input('password'),
        );
        $user = $this->userService->store($dto);

        return response()->json([
            'message' => __('User registered successfully'),
            'user' => UserResource::make($user),
        ], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {

        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();
            $token = $user->createToken(config('app.name'))->plainTextToken;

            return response()->json([
                'message' => __('Login successful'),
                'token' => $token,
            ]);
        }

        return response()->json([
            'message' => __('Unauthorized'),
        ], 401);
    }
}
