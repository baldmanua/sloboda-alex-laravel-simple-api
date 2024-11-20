<?php

namespace App\Http\Controllers;

use App\DTOs\MessageFiltersDTO;
use App\Http\Requests\StoreMessageRequest;
use App\Http\Resources\MessageResource;
use App\Models\Message;
use App\Services\MessageService;
use App\DTOs\MessageDTO;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MessageController extends Controller
{

    public function __construct(
        protected MessageService $messageService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Message::class);

        $filters = new MessageFiltersDTO(
            tags: $request->input('tags'),
            user_ids: $request->input('user_ids'),
            date_from: $request->input('date_from'),
            date_to: $request->input('date_to')
        );

        $messages = $this->messageService->find($filters);

        return response()->json(MessageResource::collection($messages));
    }

    /**
     * @throws AuthorizationException
     */
    public function show(Message $message): JsonResponse
    {
        $this->authorize('view', $message);

        return response()->json(MessageResource::make($message));
    }

    /**
     * @throws AuthorizationException
     */
    public function store(StoreMessageRequest $request): JsonResponse
    {
        $this->authorize('create', Message::class);

        $dto = new MessageDTO(
            user_id: $request->user()->id,
            message: $request->input('message'),
            tags: $request->input('tags')
        );
        $message = $this->messageService->store($dto);

        return response()->json(MessageResource::make($message));
    }

    /**
     * @throws AuthorizationException
     */
    public function update(StoreMessageRequest $request, Message $message): JsonResponse
    {
        $this->authorize('update', $message);

        $dto = new MessageDTO($request->user()->id, $request->input('message'), $request->input('tags'));
        $message = $this->messageService->update($message->id, $dto);

        return response()->json(MessageResource::make($message));
    }

    /**
     * @throws AuthorizationException
     */
    public function destroy(Message $message): JsonResponse
    {
        $this->authorize('delete', $message);

        $this->messageService->delete($message->id);

        return response()->json([

        ]);
    }
}
