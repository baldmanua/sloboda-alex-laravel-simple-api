<?php

namespace App\DTOs;

use Carbon\Carbon;
use GuzzleHttp\Psr7\Request;

class MessageFiltersDTO
{
    public ?array $tags = null;
    public ?array $user_ids = null;
    public ?Carbon $date_from = null;
    public ?Carbon $date_to = null;

    public function __construct(
        ?array $tags = null,
        ?array $user_ids = null,
        ?string $date_from = null,
        ?string $date_to = null
    ) {
        $this->tags = $tags;
        $this->user_ids = $user_ids;
        $this->date_from = $date_from ? Carbon::parse($date_from) : null;
        $this->date_to = $date_to ? Carbon::parse($date_to) : null;
    }

}
