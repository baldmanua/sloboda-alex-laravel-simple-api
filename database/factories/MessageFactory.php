<?php

namespace Database\Factories;

use App\Models\Message;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Message>
 */
class MessageFactory extends Factory
{

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'message' => fake()->text(240),
            'user_id' => User::pluck('id')->random()
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Message $message) {
            $tags = Tag::inRandomOrder()->take(rand(1, 3))->get();

            $message->tags()->attach($tags);
        });
    }
}
