<?php

use Illuminate\Support\Facades\Broadcast;
use App\Broadcasting\UserChannel;
use App\Broadcasting\ChatChannel;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Broadcast::channel('user.{user_id}', UserChannel::class);

Broadcast::channel('chatToUser.from.{user_id}.to.{another_user_id}', function ($user, $user_id, $another_user_id) {
    return $user->id == $user_id;
});

Broadcast::channel('user.{user_id}.chats', function ($user, $user_id) {
    return $user->id == $user_id;
});

Broadcast::channel('chat.{chat_id}', ChatChannel::class);
