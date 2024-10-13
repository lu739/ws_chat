<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function statuses()
    {
        return $this
            ->belongsToMany(User::class, 'message_status', 'message_id', 'user_id')
            ->withPivot('chat_id');
    }

    public function isOwner(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->user_id === auth()->id(),
        );
    }
}
