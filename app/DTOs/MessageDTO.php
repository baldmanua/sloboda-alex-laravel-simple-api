<?php

namespace App\DTOs;

class MessageDTO
{

    public function __construct(
        public readonly int $user_id,
        public readonly string $message,
        public readonly ?array $tags = null
    ) {}
}
