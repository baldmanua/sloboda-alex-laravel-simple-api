<?php

namespace App\Http\Controllers;

use App\DTOs\TagFiltersDTO;
use App\Http\Resources\TagResource;
use App\Services\TagService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TagController extends Controller
{

    public function __construct(
        protected TagService $tagService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filters = new TagFiltersDTO(
            tags: $request->input('tags'),
        );

        $tags = $this->tagService->find($filters);

        return response()->json(TagResource::collection($tags));
    }
}
