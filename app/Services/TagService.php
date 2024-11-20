<?php

namespace App\Services;

use App\DTOs\TagFiltersDTO;
use App\Helpers\EloquentHelper;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class TagService
{

    public function find(TagFiltersDTO $filters): Collection|array
    {
        $query = Tag::query();

        if (!empty($filters->name)) {
            EloquentHelper::addSearchRule($query, 'name', $filters->name);
        }

        return $query->get();
    }
}
