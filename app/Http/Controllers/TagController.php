<?php

namespace App\Http\Controllers;

use App\DTOs\TagFiltersDTO;
use App\Http\Requests\TagFiltersRequest;
use App\Http\Resources\TagResource;
use App\Services\TagService;
use Illuminate\Http\JsonResponse;

class TagController extends Controller
{

    public function __construct(
        protected TagService $tagService
    ) {}

    public function index(TagFiltersRequest $request): JsonResponse
    {
        $filters = new TagFiltersDTO(
            name: $request->input('name'),
        );

        $tags = $this->tagService->find($filters);

        return response()->json(TagResource::collection($tags));
    }
}
