<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\UserResource;

class ChatResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $user = auth()->user();
        return [
            'id' => $this->id,
            'name' => $this->name,
            'to' => new UserResource($this->toUser),
            'from' => new UserResource($this->fromUser),
            'new_messages' => $this->when($request->input('toUser', null), 
            $this->messages->where('user_id', '!=', $request->toUser)
                ->where('unread', 1)
                ->count(),
            $this->messages->where('user_id', '!=', $user->id)
                ->where('unread', 1)
                ->count())
        ];
    }
}
