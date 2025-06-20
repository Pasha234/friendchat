<?php

namespace Tests\Unit\Broadcasting;

use App\Broadcasting\ChatChannel;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChatChannelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_authorizes_a_user_who_is_the_sender_in_the_chat()
    {
        $sender = User::factory()->create();
        $receiver = User::factory()->create();
        $chat = Chat::factory()->create([
            'from' => $sender->id,
            'to' => $receiver->id,
        ]);

        $channel = new ChatChannel();

        $this->assertTrue($channel->join($sender, $chat->id));
    }

    /** @test */
    public function it_authorizes_a_user_who_is_the_receiver_in_the_chat()
    {
        $sender = User::factory()->create();
        $receiver = User::factory()->create();
        $chat = Chat::factory()->create([
            'from' => $sender->id,
            'to' => $receiver->id,
        ]);

        $channel = new ChatChannel();

        $this->assertTrue($channel->join($receiver, $chat->id));
    }

    /** @test */
    public function it_denies_a_user_who_is_not_part_of_the_chat()
    {
        $sender = User::factory()->create();
        $receiver = User::factory()->create();
        $unrelatedUser = User::factory()->create();
        $chat = Chat::factory()->create([
            'from' => $sender->id,
            'to' => $receiver->id,
        ]);

        $channel = new ChatChannel();

        $this->assertFalse($channel->join($unrelatedUser, $chat->id));
    }

    /** @test */
    public function it_denies_access_if_the_chat_does_not_exist()
    {
        $user = User::factory()->create();
        $nonExistentChatId = 999;
        $channel = new ChatChannel();
        $this->assertFalse($channel->join($user, $nonExistentChatId));
    }
}
