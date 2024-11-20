<?php

namespace App\Http\Controllers;

use App\DTOs\UserFiltersDTO;
use App\Http\Requests\UserFiltersRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{

    public function __construct(
        protected UserService $userService
    ) {}

    public function index(UserFiltersRequest $request): JsonResponse
    {
        $filters = new UserFiltersDTO(
            name: $request->input('name'),
            email: $request->input('email')
        );
        $users = $this->userService->find($filters);

        return response()->json(UserResource::collection($users));
    }
}
