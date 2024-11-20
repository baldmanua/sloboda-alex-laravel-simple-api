<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Hash;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function createUserAbdGetToken(): string
    {
        $authUser = User::factory()->create([
            'email' => 'authuser@example.com',
            'password' => Hash::make('password123'),
        ]);

        return $authUser->createToken('Test Token')->plainTextToken;
    }
}
