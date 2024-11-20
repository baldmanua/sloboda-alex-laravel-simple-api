<?php

namespace App\Services;

use App\DTOs\UserDTO;
use App\DTOs\UserFiltersDTO;
use App\Helpers\EloquentHelper;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class UserService
{

    public function find(UserFiltersDTO $filters): Collection|array
    {
        $query = User::query();

        if (!empty($filters->email)) {
            EloquentHelper::addSearchRule($query, 'email', $filters->email);
        }

        if (!empty($filters->name)) {
            EloquentHelper::addSearchRule($query, 'name', $filters->name);
        }

        return $query->get();
    }

    public function store(UserDTO $data): User
    {
        /** @var User $user */
        $user = User::create([
            'name' => $data->name,
            'email' => $data->email,
            'password' => $data->hashed_password,
        ]);

        return $user;
    }
}
