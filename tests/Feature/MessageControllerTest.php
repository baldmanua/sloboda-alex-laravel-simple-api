<?php

use App\Models\Message;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MessageControllerTest extends TestCase
{
    use RefreshDatabase;

    protected array $messageStructure = [
        'id',
        'message',
        'created_at',
        'updated_at',
        'tags' => [
            '*' => [
                'id',
                'name',
            ],
        ],
        'user' => [
            'id',
            'name',
            'email',
        ],
    ];

    protected function createTestMessages(): array
    {
        $user1 = User::factory()->create(['email' => 'user1@example.com']);
        $user2 = User::factory()->create(['email' => 'user2@example.com']);

        $tag1 = Tag::factory()->create(['name' => 'Tech']);
        $tag2 = Tag::factory()->create(['name' => 'Health']);

        $message1 = Message::factory()->create([
            'user_id' => $user1->id,
            'created_at' => now()->subDays(5),
        ]);
        $message1->tags()->attach([$tag1->id, $tag2->id]);

        $message2 = Message::factory()->create([
            'user_id' => $user2->id,
            'created_at' => now()->subDays(10),
        ]);
        $message2->tags()->attach([$tag1->id]);

        $message3 = Message::factory()->create([
            'user_id' => $user1->id,
            'created_at' => now()->subDays(1),
        ]);
        $message3->tags()->attach([$tag1->id]);

        return compact('user1', 'user2', 'tag1', 'tag2', 'message1', 'message2', 'message3');
    }

    public function test_messages_filtering_works()
    {
        $data = $this->createTestMessages();
        $message1 = $data['message1'];
        $message2 = $data['message2'];
        $message3 = $data['message3'];
        $tag1 = $data['tag1'];
        $user1 = $data['user1'];

        $token = $this->createUserAbdGetToken();

        $filters = [
            'tags' => [$tag1->name],
            'user_ids' => [$user1->id],
            'date_from' => now()->subDays(7)->toDateString(),
            'date_to' => now()->toDateString(),
        ];

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->getJson(route('messages.index', $filters));

        $responseData = $response->json();

        $response->assertStatus(200)
            ->assertJsonStructure(['*' => $this->messageStructure]);

        $this->assertTrue(collect($responseData)->contains('id', $message1->id));
        $this->assertTrue(collect($responseData)->contains('id', $message3->id));

        $this->assertFalse(collect($responseData)->contains('id', $message2->id));
    }

    public function test_messages_index_works()
    {
        $data = $this->createTestMessages();
        $message1 = $data['message1'];
        $message3 = $data['message3'];
        $message2 = $data['message2'];

        $token = $this->createUserAbdGetToken();

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->getJson(route('messages.index'));

        $responseData = $response->json();

        $response->assertStatus(200)
            ->assertJsonStructure(['*' => $this->messageStructure]);

        $this->assertTrue(collect($responseData)->contains('id', $message1->id));
        $this->assertTrue(collect($responseData)->contains('id', $message2->id));
        $this->assertTrue(collect($responseData)->contains('id', $message3->id));
    }

    public function test_message_view_works()
    {
        $data = $this->createTestMessages();
        $message1 = $data['message1'];

        $token = $this->createUserAbdGetToken();

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->getJson(route('messages.show', $message1->id));

        $response->assertStatus(200)
            ->assertJsonStructure($this->messageStructure)
            ->assertJsonPath('id', $message1->id);
    }

    public function test_message_creation_works()
    {
        $authUser = User::factory()->create();
        $token = $authUser->createToken('Test Token')->plainTextToken;
        $tags = Tag::factory()->count(2)->create();
        $payload = [
            'message' => 'This is a test message',
            'tags' => $tags->pluck('name')->toArray(),
        ];

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->postJson(route('messages.store'), $payload);

        $messageId = $response->json('id');

        $response->assertStatus(201)
            ->assertJsonStructure($this->messageStructure)
            ->assertJsonPath('message', $payload['message']);

        $this->assertDatabaseHas('messages', ['message' => $payload['message'], 'user_id' => $authUser->id]);

        foreach ($tags as $tag) {
            $this->assertDatabaseHas('message_tag', ['message_id' => $messageId, 'tag_id' => $tag->id]);
        }
    }

    public function test_message_update_works()
    {
        $data = $this->createTestMessages();
        $message1 = $data['message1'];
        $oldTags = $message1->tags;
        $newTag = Tag::factory()->create();

        $token = $message1->user->createToken('Test Token')->plainTextToken;

        $payload = [
            'message' => 'Updated message by owner',
            'tags' => [$newTag->name],
        ];

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->putJson(route('messages.update', $message1->id), $payload);

        $response->assertStatus(200)
            ->assertJsonPath('message', $payload['message'])
            ->assertJsonPath('tags.0.id', $newTag->id);

        $this->assertDatabaseHas('messages', ['id' => $message1->id, 'message' => $payload['message']]);

        foreach ($oldTags as $oldTag) {
            $this->assertDatabaseMissing('message_tag', ['message_id' => $message1->id, 'tag_id' => $oldTag->id]);
        }

        $this->assertDatabaseHas('message_tag', ['message_id' => $message1->id, 'tag_id' => $newTag->id]);
    }

    public function test_message_owned_by_other_user_not_updates()
    {
        $data = $this->createTestMessages();
        $message1 = $data['message1'];

        $token = $this->createUserAbdGetToken();

        $payload = ['message' => 'Attempted update by non-owner'];

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->putJson(route('messages.update', $message1->id), $payload);

        $response->assertStatus(403);

        $this->assertDatabaseHas('messages', ['id' => $message1->id, 'message' => $message1->message]);
    }

    public function test_message_tags_being_removed()
    {
        $data = $this->createTestMessages();
        $message1 = $data['message1'];
        $oldTags = $message1->tags;

        $token = $message1->user->createToken('Test Token')->plainTextToken;

        $payload = [
            'message' => $message1->message,
            'tags' => [],
        ];

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->putJson(route('messages.update', $message1->id), $payload);

        $response->assertStatus(200);

        foreach ($oldTags as $oldTag) {
            $this->assertDatabaseMissing('message_tag', ['message_id' => $message1->id, 'tag_id' => $oldTag->id]);
        }
    }

    public function test_message_tags_untouched_if_not_passed()
    {
        $data = $this->createTestMessages();
        $message1 = $data['message1'];
        $oldTags = $message1->tags;

        $token = $message1->user->createToken('Test Token')->plainTextToken;

        $payload = ['message' => 'Message updated without changing tags'];

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->putJson(route('messages.update', $message1->id), $payload);

        $response->assertStatus(200)
            ->assertJsonPath('message', $payload['message']);

        foreach ($oldTags as $tag) {
            $this->assertDatabaseHas('message_tag', ['message_id' => $message1->id, 'tag_id' => $tag->id]);
        }
    }

    public function test_message_destroy_works()
    {
        $data = $this->createTestMessages();
        $message1 = $data['message1'];
        $oldTags = $message1->tags;

        $token = $message1->user->createToken('Test Token')->plainTextToken;

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->deleteJson(route('messages.destroy', $message1->id));

        $response->assertStatus(204);

        $this->assertDatabaseMissing('messages', ['id' => $message1->id]);

        foreach ($oldTags as $tag) {
            $this->assertDatabaseMissing('message_tag', ['message_id' => $message1->id, 'tag_id' => $tag->id]);
        }
    }

    public function test_message_owned_by_other_user_not_destroys()
    {
        $data = $this->createTestMessages();
        $message1 = $data['message1'];

        $token = $this->createUserAbdGetToken();

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->deleteJson(route('messages.destroy', $message1->id));

        $response->assertStatus(403);

        $this->assertDatabaseHas('messages', ['id' => $message1->id]);
    }
}
