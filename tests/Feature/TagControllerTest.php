<?php


use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_tags_filtering_works()
    {
        $tag1 = Tag::factory()->create(['name' => 'Technology']);
        $tag2 = Tag::factory()->create(['name' => 'Science']);
        $tag3 = Tag::factory()->create(['name' => 'Health']);

        $token = $this->createUserAbdGetToken();

        $filters = ['name' => 'Tech'];

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->getJson(route('tags.index', $filters));

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'Technology'])
            ->assertJsonMissing(['name' => 'Science'])
            ->assertJsonMissing(['name' => 'Health']);
    }

    public function test_tags_index_works()
    {
        $tag1 = Tag::factory()->create(['name' => 'Technology']);
        $tag2 = Tag::factory()->create(['name' => 'Science']);
        $tag3 = Tag::factory()->create(['name' => 'Health']);

        $token = $this->createUserAbdGetToken();

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->getJson(route('tags.index'));

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'Technology'])
            ->assertJsonFragment(['name' => 'Science'])
            ->assertJsonFragment(['name' => 'Health']);
    }
}
