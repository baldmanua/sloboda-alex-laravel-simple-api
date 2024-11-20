<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register()
    {
        $data = [
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson(route('register'), $data);

        $this->assertDatabaseHas('users', [
            'email' => $data['email'],
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'message' => __('User registered successfully'),
                'user' => [
                    'email' => $data['email'],
                    'name' => $data['name']
                ]
            ]);
    }

    public function test_user_can_auth()
    {
        $user = User::factory()->create([
            'password' => Hash::make('password123'),
        ]);

        $data = [
            'email' => $user->email,
            'password' => 'password123',
        ];

        $response = $this->postJson(route('login'), $data);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'token',
            ]);
    }

    public function test_user_cant_login_with_wrong_credentials()
    {
        $data = [
            'email' => 'nonexistentuser@example.com',
            'password' => 'wrongpassword',
        ];

        $response = $this->postJson(route('login'), $data);

        $response->assertStatus(401);
    }
}
