<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Database\Factories\MessageFactory;

class Message extends Model
{
    use HasFactory;

    /**
     * Retrieve the user that sent the message
     */
    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function factory(): MessageFactory
    {
        return MessageFactory::new();
    }
}
