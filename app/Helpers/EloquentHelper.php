<?php

namespace App\Helpers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class EloquentHelper
{
    public static function addSearchRule(Builder $query, string $field, string $param): void
    {
        $query->where(DB::raw("LOWER({$field})"), 'like', mb_strtolower($param) . '%');
    }
}
