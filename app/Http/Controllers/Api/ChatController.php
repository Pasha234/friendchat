<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\MessageResource;
use App\Http\Resources\ChatResource;
use App\Models\Message;
use App\Models\Chat;
use App\Models\User;
use App\Events\NewMessage;
use App\Events\NewMessageInChats;

class ChatController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Display all messages referenced to the specific chat
     */
    public function showMessages($user_id, $user_id_2) {
        $user = auth()->user();
        if ($user->id == $user_id || $user->id == $user_id_2) {
            $chat = Chat::where(function($query) use ($user_id, $user_id_2) {
                $query->where(function($query) use ($user_id, $user_id_2) {
                    $query->where('to', $user_id)
                        ->where('from', $user_id_2);
                })
                ->orWhere(function($query) use ($user_id, $user_id_2) {
                    $query->where('from', $user_id)
                        ->where('to', $user_id_2);
                });
            })
            ->first();
            if (!$chat) {
                return response()->json([
                    'error' => 'There is no chat between these two users',
                ], 404);
            }
            Message::where('chat_id', $chat->id)
                ->where('user_id', '!=', $user->id)
                ->update(['unread' => 0]);
            return response()->json([
                'chat' => new ChatResource($chat),
                'messages' => MessageResource::collection(
                $chat->messages
            )]);
        }
        return response()->json([
            'error' => 'Forbidden'
        ], 403);
    }

    /**
     * Send message to the chat
     * @param Request $request
     * @param int $id
     */
    public function sendMessage(Request $request) {
        $validated = $request->validate([
            'body' => 'required|string',
            'chat_id' => 'exists:chats,id|integer|nullable',
            'to' => 'exists:users,id|integer|nullable',
            'from' => 'exists:users,id|integer',
        ]);

        $user = auth()->user();
        if ($user->id != $validated['from']) {
            return response()->json([
                'error' => 'Forbidden'
            ], 403);
        }
        if (is_null($request->chat_id)) {
            $chat = new Chat;
            $chat->name = 'Test';
            $chat->from = $request->from;
            $chat->to = $request->to;
            $chat->save();
            $message = new Message;
            $message->body = $request->body;
            $message->chat_id = $chat->id;
            $message->user_id = $request->from;
            $message->save();
            event(new NewMessage(User::find($validated['to']), User::find($validated['from']), $message));
            $request->merge(['toUser', $request->to]);
            event(new NewMessageInChats(User::find($validated['to']), $chat));
            return response()->json([
                'chat' => new ChatResource($chat),
                'success' => true,
            ]);
        } else {
            $chat = Chat::find($validated['chat_id']);
            if ($chat->to == $validated['from'] || $chat->from == $validated['from']) {
                if ($chat->to == $validated['from']) {
                    $to = $chat->fromUser;
                } else {
                    $to = $chat->toUser;
                }
                $message = new Message;
                $message->body = $request->body;
                $message->chat_id = $request->chat_id;
                $message->user_id = $request->from;
                $message->save();
                event(new NewMessage($to, User::find($validated['from']), $message));
                $request->merge(['toUser' => $to->id]);
                event(new NewMessageInChats($to, $chat));
                return response()->json([
                    'success' => true
                ]);
            } else {
                return response()->json([
                    'error' => 'Forbidden'
                ], 403);
            }
        }
    }

    /**
     * Retrieve all chats associated with user
     * 
     * @param integer $id
     * @return \Illuminate\Http\Response
     */
    public function userChats() {
        $user = auth()->user();
        return ChatResource::collection(
            Chat::where(function($query) use ($user) {
                $query->where('to', $user->id)
                    ->orWhere('from', $user->id);
                })
                ->get()
        );
    }
}