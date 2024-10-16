<?php

use App\Broadcasting\ChatChannel;
use App\Broadcasting\ChatsUserChannel;
use Illuminate\Support\Facades\Broadcast;

// Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
//     return (int) $user->id === (int) $id;
// });

Broadcast::channel('chat.{chatId}', ChatChannel::class);
Broadcast::channel('chat.changes.for.user.{userId}', ChatsUserChannel::class);
