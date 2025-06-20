<?php

namespace Tests\Unit\Broadcasting;

use App\Broadcasting\UserChannel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserChannelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_authorizes_a_user_to_join_their_own_channel()
    {
        $user = User::factory()->create();
        $channel = new UserChannel();

        $this->assertTrue($channel->join($user, $user->id));
    }

    /** @test */
    public function it_denies_a_user_from_joining_another_users_channel()
    {
        $user = User::factory()->create();
        $anotherUserId = User::factory()->create()->id;
        $channel = new UserChannel();

        $this->assertFalse($channel->join($user, $anotherUserId));
    }
}
