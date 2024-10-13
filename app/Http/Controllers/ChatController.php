<?php

namespace App\Http\Controllers;

use App\Http\Requests\Chat\StoreRequest;
use App\Http\Resources\Chat\ChatResource;
use App\Http\Resources\User\UserResource;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    public function index()
    {
        $users = UserResource::collection(User::query()
            ->where('id', '!=', auth()->id())
            ->get()
        )->resolve();

        $chats = auth()->user()->chats()
            ->whereHas('messages')
            ->get();

        return inertia('Chat/Index', compact('users', 'chats'));
    }


    public function show(Chat $chat)
    {
        $chat = ChatResource::make($chat->load('users', 'messages'))->resolve();

        return inertia('Chat/Show', [
            'chat' => $chat,
        ]);
    }


    public function store(StoreRequest $request)
    {
        $data = $request->validated();
        $userIds = array_merge($data['users'], [auth()->id()]);
        sort($userIds);
        $data['users_str'] = implode('-', $userIds);
        unset($data['users']);

        try {
            DB::beginTransaction();

                $chat = Chat::where(['users_str' => $data['users_str']])->first();

                if (!$chat) {
                    $chat = Chat::create($data);
                    $chat->users()->sync($userIds);
                }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('chats.index')->withErrors(['Error' => $e->getMessage()]);
        }

        return redirect()->route('chats.show', $chat);
    }
}
