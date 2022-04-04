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
    public function join(User $user, $chat_id)
    {
        $chat = Chat::find($chat_id);
        return ($user->id == $chat->to || $user->id == $chat->from);
    }
}
