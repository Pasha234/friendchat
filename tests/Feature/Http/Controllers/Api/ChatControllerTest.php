<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Events\NewMessage;
use App\Events\NewMessageInChats;
use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class ChatControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function show_messages_returns_messages_for_a_valid_chat()
    {
        $user1 = User::factory()->createOne();
        $user2 = User::factory()->createOne();

        $chat = Chat::factory()->create([
            'from' => $user1->id,
            'to' => $user2->id,
        ]);

        Message::factory()->count(10)->create([
            'chat_id' => $chat->id,
            'user_id' => $user2->id,
            'unread' => true,
        ]);

        $this->actingAs($user1, 'sanctum');

        $response = $this->getJson("/api/chat/to/{$user1->id}/from/{$user2->id}/messages");

        $response->assertOk()
            ->assertJsonStructure([
                'chat' => ['id', 'name', 'to', 'from'],
                'messages' => [['id', 'body', 'user']],
            ])
            ->assertJsonCount(10, 'messages');

        $this->assertDatabaseHas('messages', [
            'chat_id' => $chat->id,
            'user_id' => $user2->id,
            'unread' => false,
        ]);
    }

    /**
     * @test
     */
    public function show_messages_is_forbidden_for_unrelated_user()
    {
        $user1 = User::factory()->createOne();
        $user2 = User::factory()->createOne();
        $unrelatedUser = User::factory()->createOne();

        Chat::factory()->create([
            'from' => $user1->id,
            'to' => $user2->id,
        ]);

        $this->actingAs($unrelatedUser, 'sanctum');

        $response = $this->getJson("/api/chat/to/{$user1->id}/from/{$user2->id}/messages");

        $response->assertForbidden();
    }

    /**
     * @test
     */
    public function show_messages_returns_404_if_chat_does_not_exist()
    {
        $user1 = User::factory()->createOne();
        $user2 = User::factory()->createOne();

        $this->actingAs($user1, 'sanctum');

        $response = $this->getJson("/api/chat/to/{$user1->id}/from/{$user2->id}/messages");

        $response->assertNotFound();
    }

    /**
     * @test
     */
    public function send_message_to_existing_chat_succeeds()
    {
        Event::fake();

        $user1 = User::factory()->createOne();
        $user2 = User::factory()->createOne();
        $chat = Chat::factory()->createOne(['from' => $user1->id, 'to' => $user2->id]);

        $this->actingAs($user1, 'sanctum');

        $payload = [
            'body' => 'Hello there!',
            'chat_id' => $chat->id,
            'from' => $user1->id,
        ];

        $response = $this->postJson('/api/messages/send', $payload);

        $response->assertOk()->assertJson(['success' => true]);

        $this->assertDatabaseHas('messages', [
            'body' => 'Hello there!',
            'chat_id' => $chat->id,
            'user_id' => $user1->id,
        ]);

        Event::assertDispatched(NewMessage::class);
        Event::assertDispatched(NewMessageInChats::class);
    }

    /**
     * @test
     */
    public function send_message_creates_a_new_chat_if_one_does_not_exist()
    {
        Event::fake();

        $user1 = User::factory()->createOne();
        $user2 = User::factory()->createOne();

        $this->actingAs($user1, 'sanctum');

        $payload = [
            'body' => 'This is the first message.',
            'from' => $user1->id,
            'to' => $user2->id,
        ];

        $response = $this->postJson('/api/messages/send', $payload);

        $response->assertOk()
            ->assertJsonStructure([
                'chat' => ['id'],
                'success'
            ]);

        $this->assertDatabaseCount('chats', 1);
        $this->assertDatabaseHas('chats', [
            'from' => $user1->id,
            'to' => $user2->id,
        ]);
        $this->assertDatabaseHas('messages', [
            'body' => 'This is the first message.',
            'user_id' => $user1->id,
        ]);

        Event::assertDispatched(NewMessage::class);
        Event::assertDispatched(NewMessageInChats::class);
    }

    /**
     * @test
     */
    public function send_message_is_forbidden_if_from_id_does_not_match_auth_user()
    {
        $user1 = User::factory()->createOne();
        $user2 = User::factory()->createOne();

        $this->actingAs($user1, 'sanctum');

        $payload = [
            'body' => 'Trying to send as someone else.',
            'from' => $user2->id,
            'to' => $user1->id,
        ];

        $response = $this->postJson('/api/messages/send', $payload);

        $response->assertForbidden();
    }

    /**
     * @test
     */
    public function user_chats_returns_all_chats_for_the_authenticated_user()
    {
        $user = User::factory()->createOne();
        $otherUser1 = User::factory()->createOne();
        $otherUser2 = User::factory()->createOne();

        // Create a chat where the user is the sender
        Chat::factory()->create(['from' => $user->id, 'to' => $otherUser1->id]);

        // Create a chat where the user is the receiver
        Chat::factory()->create(['from' => $otherUser2->id, 'to' => $user->id]);

        // Create an unrelated chat
        Chat::factory()->create();

        $this->actingAs($user, 'sanctum');

        $response = $this->getJson('/api/user/chats');

        $response->assertOk()
            ->assertJsonCount(2, 'data');
    }

    /**
     * @test
     */
    public function user_chats_returns_empty_array_for_user_with_no_chats()
    {
        $user = User::factory()->createOne();
        $this->actingAs($user, 'sanctum');

        $response = $this->getJson('/api/user/chats');

        $response->assertOk()
            ->assertJsonCount(0, 'data');
    }
}
