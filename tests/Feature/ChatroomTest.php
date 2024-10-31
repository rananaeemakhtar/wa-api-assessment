<?php

namespace Tests\Feature\Feature;

use App\Models\Chatroom;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use App\Models\User;

class ChatroomTest extends TestCase
{
    // use RefreshDatabase;

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

    public function test_create_chatroom_validates_data(): void
    {
        $response = $this->post('/api/chatrooms', [], ['Accept' => 'Application/Json']);

        $response->assertStatus(422);
        // $response->assertJsonFragment(['message' => 'Chatroom Created', 'status' => true]);
    }

    public function test_user_can_create_chatroom(): void
    {
        $response = $this->post('/api/chatrooms', [
            'name'      =>      'Chatroom'
        ], ['Accept' => 'Application/Json']);

        $response->assertStatus(201);
        $response->assertJsonFragment(['message' => 'Chatroom Created', 'status' => true]);
    }

    public function test_created_user_exists_in_chatrooms_table(): void
    {
        $this->post('/api/chatrooms', [
            'name'      =>      'Chatroom'
        ], ['Accept' => 'Application/Json']);

        $this->assertDatabaseHas('chatrooms', [
            'name' => 'Chatroom'
        ]);
    }

    public function test_user_can_get_list_of_chatrooms(): void
    {
        Chatroom::factory()->create(['name' => 'General']);
        Chatroom::factory()->create(['name' => 'Random']);
        Chatroom::factory()->create(['name' => 'Announcements']);

        $response = $this->get('/api/chatrooms');

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'message' => 'Chatroom List',
            'status' => true,
        ]);

        $response->assertJsonFragment(['name' => 'General']);
        $response->assertJsonFragment(['name' => 'Random']);
        $response->assertJsonFragment(['name' => 'Announcements']);
    }

    
    public function test_user_can_enter_to_a_chatroom(): void
    {
        $chatroom = Chatroom::factory()->create();
        
        $response = $this->postJson(route('chatrooms.enter', $chatroom));

        $response->assertStatus(201);
        $response->assertJson([
            'message' => 'Entered Chatroom',
            'status' => true,
        ]);

        $this->assertDatabaseHas('chatroom_users', [
            'user_id' => $this->user->id,
            'chatroom_id' => $chatroom->id,
        ]);
    }


    public function test_user_can_leave_a_chatroom(): void
    {
        $chatroom = Chatroom::factory()->create();

        $chatroom->users()->attach($this->user->id);

        $response = $this->postJson(route('chatrooms.leave', $chatroom));

        $response->assertStatus(201);
        $response->assertJson([
            'message' => 'Left Chatroom',
            'status' => true,
        ]);

        // Assert: Check that the user is no longer associated with the chatroom
        $this->assertDatabaseMissing('chatroom_users', [
            'user_id' => $this->user->id,
            'chatroom_id' => $chatroom->id,
        ]);
    }
}
