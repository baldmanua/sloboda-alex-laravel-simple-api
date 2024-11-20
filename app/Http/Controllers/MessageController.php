<?php

namespace App\Http\Controllers;

use App\DTOs\MessageFiltersDTO;
use App\Http\Requests\MessageFiltersRequest;
use App\Http\Requests\MessageRequest;
use App\Http\Resources\MessageResource;
use App\Models\Message;
use App\Services\MessageService;
use App\DTOs\MessageDTO;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class MessageController extends Controller
{

    public function __construct(
        protected MessageService $messageService
    ) {
        $this->authorizeResource(Message::class, 'message');
    }

    public function index(MessageFiltersRequest $request): JsonResponse
    {
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
        return response()->json(MessageResource::make($message));
    }

    public function store(MessageRequest $request): JsonResponse
    {
        $dto = new MessageDTO(
            user_id: $request->user()->id,
            message: $request->input('message'),
            tags: $request->input('tags')
        );
        $message = $this->messageService->store($dto);

        return response()->json(MessageResource::make($message), 201);
    }

    public function update(MessageRequest $request, Message $message): JsonResponse
    {
        $dto = new MessageDTO(
            user_id: $request->user()->id,
            message: $request->input('message'),
            tags: $request->input('tags')
        );
        $message = $this->messageService->update($message->id, $dto);

        return response()->json(MessageResource::make($message));
    }

    public function destroy(Message $message): Response
    {
        $this->messageService->delete($message->id);

        return response()->noContent();
    }
}
