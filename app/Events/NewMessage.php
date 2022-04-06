<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Http\Resources\MessageResource;
use App\Http\Resources\UserResource;

class NewMessage implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user, $message;

    /**
     * Create a new event instance.
     * @param App\Models\User $to
     * @param App\Models\User $from
     * @param App\Models\Message $message
     * @return void
     */
    public function __construct($to, $from, $message)
    {
        $this->to = $to;
        $this->from = $from;
        $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return [
            new PrivateChannel('chatToUser.from.' . $this->from->id . '.to.' . $this->to->id),
            new PrivateChannel('chatToUser.from.' . $this->to->id . '.to.' . $this->from->id),
        ];
    }

    public function broadcastWith() {
        return [
            'user' => new UserResource($this->from),
            'message' => new MessageResource($this->message),
        ];
    }
}
