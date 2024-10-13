<?php

namespace App\Http\Controllers;

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
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            return response()
                ->json(['message' => $e->getMessage()], 500);
        }

        // broadcast(new \App\Events\MessageCreated($message))->toOthers();

        return MessageResource::make($message)->resolve();
    }
}
