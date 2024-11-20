<?php

namespace App\Services;

use App\DTOs\TagFiltersDTO;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Collection;

class TagService
{

    public function find(TagFiltersDTO $filters): Collection|array
    {
        $query = Tag::query();

        if (!empty($filters->tags)) {
            $query->whereIn('name', $filters->tags);
        }

        return $query->get();
    }
}
