<?php

namespace App\DTOs;

class MessageFiltersDTO
{
    public function __construct(
        public readonly ?array $tags = null,
        public readonly ?array $user_ids = null,
        public readonly ?string $date_from = null,
        public readonly ?string $date_to = null
    ) {}

}
