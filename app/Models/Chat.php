<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
    public function otherUsers()
    {
        return $this->users()->where('user_id', '!=', auth()->id());
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function messageStatus()
    {
        return $this->hasMany(MessageStatus::class);
    }

    public function unreadMessages(int $userId = null)
    {
        $userId = $userId ?: auth()->id();

        return $this->messageStatus()
            ->where('user_id', '=', $userId)
            ->where('is_read', 0);
    }

    public function lastMessage(): ?string
    {
        return $this->messages()->latest()->first()->body ?? null;
    }
}
