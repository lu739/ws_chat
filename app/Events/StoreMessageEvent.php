<?php

namespace App\Events;

use App\Http\Resources\Message\MessageResource;
use App\Models\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;


class StoreMessageEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(private Message $message)
    {
    }


    public function broadcastOn(): PrivateChannel
    // public function broadcastOn(): array
    {
        return new PrivateChannel('chat.' . $this->message->chat_id);

        // return [
        //     new Channel('store-message' . $this->message->chat_id),
        // ];
    }

    public function broadcastAs(): string
    {
        return 'storeMessage';
    }

    public function broadcastWith(): array
    {
        return [
            'message' => MessageResource::make($this->message)->resolve(),
        ];
    }
}
