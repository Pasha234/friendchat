<?php

namespace Database\Factories;

use App\Models\Message;
use App\Models\User;
use App\Models\Chat;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @template-extends Factory<Message>
 */
class MessageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Message::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(), // Assumes you have a UserFactory
            'chat_id' => Chat::factory(), // Assumes you have a ChatFactory
            'body' => $this->faker->sentence(10),
            'unread' => $this->faker->boolean(75), // 75% chance of being unread
            // 'created_at' and 'updated_at' will be handled by Laravel automatically
        ];
    }
}
