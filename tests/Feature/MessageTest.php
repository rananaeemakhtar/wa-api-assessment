<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Chatroom;
use App\Models\Message;
use App\Models\Attachment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MessageTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('migrate:fresh');

        $this->user = User::create([
            'email'    => 'test@test.com',
            'name'     => 'Test User',
            'password' => bcrypt('Test')
        ]);

        Sanctum::actingAs($this->user);

        $this->assertAuthenticated();
    }

    public function test_user_can_send_message_with_attachments()
    {
        $chatroom = Chatroom::factory()->create();

        $attachment = UploadedFile::fake()->image('test-image.jpg');
        $document = UploadedFile::fake()->create('test-document.pdf');

        $response = $this->post('/api/chatrooms/' . $chatroom->id . '/messages', [
            'chatroom_id' => $chatroom->id,
            'content' => 'Hello, this is a test message.',
            'attachments' => [$attachment, $document],
        ]);

        $response->assertStatus(201);
        $response->assertJson([
            'message' => 'Message sent successfully',
            'status' => true,
        ]);

        $this->assertDatabaseHas('messages', [
            'chatroom_id' => $chatroom->id,
            'user_id' => $this->user->id,
            'content' => 'Hello, this is a test message.',
        ]);

        $this->assertCount(2, Attachment::all());
        $this->assertDatabaseHas('attachments', [
            'message_id' => Message::first()->id,
            'original_name' => 'test-image.jpg',
        ]);
        
        $this->assertDatabaseHas('attachments', [
            'message_id' => Message::first()->id,
            'original_name' => 'test-document.pdf',
        ]);
    }
}
