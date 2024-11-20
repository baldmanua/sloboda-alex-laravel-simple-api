<?php

namespace App\DTOs;

class TagFiltersDTO
{

    public function __construct(
        public readonly ?string $name
    ) {}
}
