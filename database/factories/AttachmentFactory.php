<?php

namespace Database\Factories;

use App\Models\Message;
use Illuminate\Database\Eloquent\Factories\Factory;
use Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Attachment>
 */
class AttachmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $fileType = $this->faker->randomElement(['image/jpeg', 'video/mp4', 'application/pdf', 'text/plain']);
        $filePath = match (true) {
            str_contains($fileType, 'image') => 'pictures/' . Str::random(10) . '.jpg',
            str_contains($fileType, 'video') => 'videos/' . Str::random(10) . '.mp4',
            str_contains($fileType, 'application') => 'documents/' . Str::random(10) . '.pdf',
            str_contains($fileType, 'text') => 'documents/' . Str::random(10) . '.txt',
            default => 'documents/' . Str::random(10),
        };

        return [
            'message_id' => Message::factory(), 
            'file_path' => $filePath,
            'file_type' => $fileType,
            'original_name' => $this->faker->lexify('file_????.' . pathinfo($filePath, PATHINFO_EXTENSION)),
        ];
    }
}
