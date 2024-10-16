<?php

namespace App\Http\Controllers;

use App\Events\StoreMessageStatusEvent;
use App\Http\Requests\Chat\StoreRequest;
use App\Http\Resources\Chat\ChatMinifiedResource;
use App\Http\Resources\Chat\ChatResource;
use App\Http\Resources\Message\MessageResource;
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

        $chats = ChatResource::collection(auth()->user()->chats()
            ->whereHas('messages')
            ->with('users', 'unreadMessages')
            ->get())->resolve();

        return inertia('Chat/Index', compact('users', 'chats'));
    }


    public function show(Chat $chat)
    {
        if (!auth()->user()->chats()->where('chats.id', $chat->id)->exists()) {
            return redirect()->route('chats.index');
        }

        $hasUnread = $chat->unreadMessages()->count() > 0;
        if ($hasUnread) {
            $chat->unreadMessages()->update(['is_read' => 1]);

            $otherUsers = $chat->otherUsers;
            foreach ($otherUsers as $user) {
                broadcast(new StoreMessageStatusEvent(
                    $user->id,
                    $chat,
                    $chat->unreadMessages()->count(),
                    $chat->lastMessage(),
                ));
            }
        }

        $page = request('page') ?? 1;
        $isLoadMore = request('load_more') ?? false;

        $messages = $chat
            ->messages()
            ->with('user')
            ->orderByDesc('created_at')
            ->paginate(5, ['*'], 'page', $page);

        if ($isLoadMore) {
            return response()->json([
                'messages' => MessageResource::collection($messages->items())->resolve(),
                'last_page' => $messages->lastPage(),
                'current_page' => $messages->currentPage(),
            ]);
        }

        $chat = ChatMinifiedResource::make($chat->load('users'))->resolve();
        $messages = MessageResource::collection($messages)->resolve();

        return inertia('Chat/Show', [
            'current_page' => $page,
            'chat' => $chat,
            'messages' => $messages,
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
