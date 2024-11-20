<?php

namespace App\Services;

use App\DTOs\MessageDTO;
use App\DTOs\MessageFiltersDTO;
use App\Models\Message;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Collection;

class MessageService
{

    public function find(MessageFiltersDTO $filters): Collection|array
    {
        $query = Message::query();

        if ($tags = $filters->tags) {
            $query->whereHas('tags', function ($q) use ($tags) {
                $q->whereIn('name', $tags);
            });
        }

        if ($filters->date_from) {
            $query->where('created_at', '>=', $filters->date_from);
        }

        if ($filters->date_to) {
            $query->where('created_at', '<=', $filters->date_to);
        }

        if ($filters->user_ids) {
            $query->whereIn('user_id', $filters->user_ids);
        }

        return $query->get();
    }

    public function store(MessageDTO $data): Message
    {
        /** @var Message $message */
        $message = Message::create([
            'user_id' => $data->user_id,
            'message' => $data->message
        ]);

        $this->updateTags($message, $data->tags);

        return $message;
    }

    public function update(int $id, MessageDTO $data): Message
    {
        /** @var Message $message */
        $message = Message::findOrFail($id);

        $message->update([
            'message' => $data->message
        ]);

        $this->updateTags($message, $data->tags);

        return $message;
    }

    protected function updateTags(Message $message, ?array $tag_names): void
    {
        if (!is_null($tag_names)) {
            $tags = Tag::whereIn('name', $tag_names)->get();
            $message->tags()->sync($tags);
        }
    }

    public function delete($id): ?bool
    {
        /** @var Message $message */
        $message = Message::findOrFail($id);

        return $message->delete();
    }
}
