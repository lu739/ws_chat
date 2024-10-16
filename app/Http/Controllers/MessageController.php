<?php

namespace App\Http\Controllers;

use App\Events\StoreMessageEvent;
use App\Events\StoreMessageStatusEvent;
use App\Http\Requests\Message\StoreRequest;
use App\Http\Resources\Message\MessageResource;
use App\Models\Chat;
use App\Models\Message;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{

    public function store(StoreRequest $request)
    {
        $data = $request->validated();

        $data['user_id'] = auth()->id();

        $chat = Chat::find($data['chat_id']);
        $otherUsers = $chat->otherUsers;

        try {
            DB::beginTransaction();

            $message = Message::create($data);

            foreach ($otherUsers as $user) {
                $message->statuses()->attach($user, ['chat_id' => $chat->id]);

                broadcast(new StoreMessageStatusEvent(
                    $user->id,
                    $chat,
                    $chat->unreadMessages($user->id)->count(),
                    $chat->lastMessage(),
                ));
            }

            broadcast(new StoreMessageEvent($message))->toOthers();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            return response()
                ->json(['message' => $e->getMessage()], 500);
        }

        // event(new StoreMessageEvent($message));

        return MessageResource::make($message)->resolve();
    }

    public function readByUser(Chat $chat)
    {
        $hasUnread = $chat->unreadMessages()->count() > 0;

        if ($hasUnread) {
            $chat->unreadMessages()->update(['is_read' => 1]);

            $users = $chat->otherUsers()->get();
            foreach ($users as $user) {
                broadcast(new StoreMessageStatusEvent(
                    $user->id,
                    $chat,
                    $chat->unreadMessages($user->id)->count(),
                    $chat->lastMessage(),
                ));
            }
        }

        return response()->json(['success' => true]);
    }
}
