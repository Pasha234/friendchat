<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Message;
use Database\Factories\ChatFactory;

class Chat extends Model
{
    use HasFactory;

    /**
     * Get the first user from personal chat
     */
    public function toUser() {
        return $this->belongsTo(User::class, 'to');
    }

    /**
     * Get the second user from the personal chat
     */
    public function fromUser() {
        return $this->belongsTo(User::class, 'from');
    }


    /**
     * Display messages from the chat
     */
    public function messages() {
        return $this->hasMany(Message::class)->orderBy('created_at', 'desc');
    }

    public static function factory(): ChatFactory
    {
        return ChatFactory::new();
    }
}
