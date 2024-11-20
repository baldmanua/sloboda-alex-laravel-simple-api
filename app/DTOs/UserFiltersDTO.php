<?php

namespace App\DTOs;

class UserFiltersDTO
{

    public function __construct(
        public readonly ?string $name,
        public readonly ?string $email,
    ) {}
}
