<?php

namespace App\Http\Resources\Chat;

use App\Http\Resources\Message\MessageResource;
use App\Http\Resources\User\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'users' => UserResource::collection($this->users)->resolve(),
            'unreadMessages' => $this->unreadMessages()->count(),
            'lastMessage' => $this->lastMessage(),
        ];
    }
}
