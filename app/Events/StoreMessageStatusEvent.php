<?php

namespace App\Events;


use App\Models\Chat;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;


class StoreMessageStatusEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        private int $userId,
        private Chat $chat,
        private int $unreadMesagesCount,
        private string $lastMesage)
    {
    }


    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('chat.changes.for.user.' . $this->userId);
    }

    public function broadcastAs(): string
    {
        return 'storeMessageStatus';
    }

    public function broadcastWith(): array
    {
        return [
            'chat_id' => $this->chat->id,
            'unreadMessages' => $this->unreadMesagesCount,
            'lastMessage' => $this->lastMesage,
        ];
    }
}
