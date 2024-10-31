<?php

namespace Database\Factories;

use App\Models\Chatroom;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Chatroom>
 */
class ChatroomFactory extends Factory
{
    protected $model = Chatroom::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = User::factory()->create();

        return [
            'creator_id'    =>  $user->id,
            'name'          =>  fake()->name()
        ];
    }
}
