<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Message;
use App\Models\Chatroom;
use App\Models\Attachment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Message>
 */
class MessageFactory extends Factory
{
    protected $model = Message::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    public function definition(): array
    {
        return [
            'chatroom_id' => Chatroom::factory(),
            'user_id' => User::factory(),
            'content' => $this->faker->sentence(),
        ];
    }

    /**
     * Attach attachments to the message.
     */
    public function withAttachments(int $count = 1)
    {
        return $this->afterCreating(function (Message $message) use ($count) {
            Attachment::factory()->count($count)->create([
                'message_id' => $message->id,
            ]);
        });
    }
}
