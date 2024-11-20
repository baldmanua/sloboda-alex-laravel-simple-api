<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_filtering_works()
    {
        $user1 = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        $user2 = User::factory()->create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
        ]);

        $user3 = User::factory()->create([
            'name' => 'John Black',
            'email' => 'john.black@example.com',
        ]);

        $filters = [
            'name' => 'John',
            'email' => 'john',
        ];

        $token = $this->createUserAbdGetToken();

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->getJson(route('users.index', $filters));

        $response->assertStatus(200)
            ->assertJsonFragment([
                'name' => 'John Doe',
                'email' => 'john@example.com',
            ])
            ->assertJsonFragment([
                'name' => 'John Black',
                'email' => 'john.black@example.com',
            ])
            ->assertJsonMissing([
                'name' => 'Jane Smith',
                'email' => 'jane@example.com',
            ]);
    }

    public function test_users_index_works()
    {
        $user1 = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        $user2 = User::factory()->create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
        ]);

        $token = $this->createUserAbdGetToken();

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->getJson(route('users.index'));

        $response->assertStatus(200)
            ->assertJsonFragment([
                'name' => 'John Doe',
                'email' => 'john@example.com',
            ])
            ->assertJsonFragment([
                'name' => 'Jane Smith',
                'email' => 'jane@example.com',
            ]);
    }
}
