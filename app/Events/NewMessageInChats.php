<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Http\Resources\ChatResource;

class NewMessageInChats implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user, $chat;

    /**
     * Create a new event instance.
     * @param App\Models\User $to
     * @param App\Models\Chat $chat
     * @return void
     */
    public function __construct($to, $chat)
    {
        $this->user = $to;
        $this->chat = $chat;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('user.' . $this->user->id . '.chats');
    }

    public function broadcastWith() {
        return [
            'chat' => new ChatResource($this->chat),
        ];
    }
}
