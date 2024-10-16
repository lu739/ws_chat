<?php

namespace App\Jobs;

use App\Events\StoreMessageStatusEvent;
use App\Models\Chat;
use App\Models\Message;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Queue\Queueable;

class StoreMessageStatusJob implements ShouldQueue
{
    use Queueable;


    public function __construct(
        private Collection $otherUsers,
        private Chat $chat,
        private Message $message)
    {
    }

    public function handle(): void
    {
        foreach ($this->otherUsers as $user) {
            $this->message->statuses()->attach($user, ['chat_id' => $this->chat->id]);

            broadcast(new StoreMessageStatusEvent(
                $user->id,
                $this->chat,
                $this->chat->unreadMessages($user->id)->count(),
                $this->chat->lastMessage(),
            ));
        }
    }
}
