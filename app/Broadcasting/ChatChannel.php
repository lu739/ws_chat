<?php

namespace App\Broadcasting;

use App\Models\User;

class ChatChannel
{
    public function __construct()
    {
    }

    /**
     * Authenticate the user's access to the channel.
     */
    public function join(): bool
    {
        return auth()->check();
    }
}
