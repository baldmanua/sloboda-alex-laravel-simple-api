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
    public function store(MessageDTO $data)
    {
        $message = Message::create([
            'user_id' => $data->user_id,
            'message' => $data->message
        ]);

        if (!empty($data->tags)) {
            $tags = Tag::query()->whereIn('name', $data->tags)->get();
            $message->tags()->sync($tags);
        }

        return $message;
    }

    public function update(int $id, MessageDTO $data): ?Message
    {
        $message = Message::findOrFail($id);

        $message->update([
            'message' => $data->message
        ]);

        if (isset($data->tags)) {
            $tags = Tag::whereIn('name', $data->tags)->get();
            $message->tags()->sync($tags);
        }

        return $message;
    }

    public function delete($id)
    {
        $message = Message::findOrFail($id);

        return $message->delete();
    }
}
