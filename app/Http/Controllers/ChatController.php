<?php

namespace App\Http\Controllers;

use App\Http\Requests\Chat\StoreRequest;
use App\Http\Resources\User\UserResource;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    public function index()
    {
        $users = UserResource::collection(User::query()
            ->where('id', '!=', auth()->id())
            ->get()
        )->resolve();

        $chats = auth()->user()->chats()->get();

        return inertia('Chat/Index', compact('users', 'chats'));
    }


    public function show(Chat $chat)
    {
        return inertia('Chat/Show', [
            'chat' => $chat->load('users'),
        ]);
    }


    public function store(StoreRequest $request)
    {
        $data = $request->validated();
        $userIds = array_merge($data['users'], [auth()->id()]);
        sort($userIds);
        $data['users'] = implode('-', $userIds);

        try {
            DB::beginTransaction();

                $chat = Chat::where(['users' => $data['users']])->first();

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
