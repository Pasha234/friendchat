<?php

namespace App\Broadcasting;

use App\Models\User;
use App\Models\Chat;

class ChatChannel
{
    /**
     * Create a new channel instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Authenticate the user's access to the channel.
     *
     * @param  \App\Models\User  $user
     * @return array|bool
     */
    public function join(User $user, int $chat_id)
    {
        $chat = Chat::find($chat_id);
        if (!$chat) {
            return false;
        }
        return ($user->id == $chat->to || $user->id == $chat->from);
    }
}
