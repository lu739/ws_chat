<?php

namespace App\Broadcasting;

use App\Models\User;

class ChatsUserChannel
{
    /**
     * Create a new channel instance.
     */
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
