<?php

namespace App\DTOs;

class MessageDTO
{
    public int $user_id;
    public string $message;
    public ?array $tags;

    public function __construct(int $user_id, string $message, ?array $tags = null)
    {
        $this->user_id = $user_id;
        $this->message = $message;
        $this->tags = $tags;
    }
}
