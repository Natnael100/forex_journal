<?php

namespace Tests\Feature;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class MessagingTest extends TestCase
{
    use RefreshDatabase;

    protected $analyst;
    protected $trader;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed roles if not present (simplified for test)
        if (!Role::where('name', 'analyst')->exists()) {
             Role::create(['name' => 'analyst']);
             Role::create(['name' => 'trader']);
        }

        $this->analyst = User::factory()->create();
        $this->analyst->assignRole('analyst');

        $this->trader = User::factory()->create();
        $this->trader->assignRole('trader');
    }

    public function test_analyst_can_start_conversation_with_trader()
    {
        $response = $this->actingAs($this->analyst)
            ->post(route('conversations.store'), [
                'recipient_id' => $this->trader->id,
            ]);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('conversations', [
            'analyst_id' => $this->analyst->id,
            'trader_id' => $this->trader->id,
        ]);
    }

    public function test_trader_can_start_conversation_with_analyst()
    {
        $response = $this->actingAs($this->trader)
            ->post(route('conversations.store'), [
                'recipient_id' => $this->analyst->id,
            ]);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('conversations', [
            'analyst_id' => $this->analyst->id,
            'trader_id' => $this->trader->id,
        ]);
    }

    public function test_users_can_send_messages()
    {
        $conversation = Conversation::create([
            'analyst_id' => $this->analyst->id,
            'trader_id' => $this->trader->id,
        ]);

        $response = $this->actingAs($this->analyst)
            ->post(route('messages.store', $conversation), [
                'content' => 'Hello Trader',
            ]);

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('messages', [
            'conversation_id' => $conversation->id,
            'sender_id' => $this->analyst->id,
            'content' => 'Hello Trader',
        ]);
    }

    public function test_users_cannot_access_others_conversations()
    {
        $otherUser = User::factory()->create();
        $conversation = Conversation::create([
            'analyst_id' => $this->analyst->id,
            'trader_id' => $this->trader->id,
        ]);

        $response = $this->actingAs($otherUser)
            ->get(route('conversations.show', $conversation));

        $response->assertStatus(403);
    }

    public function test_polling_messages()
    {
        $conversation = Conversation::create([
            'analyst_id' => $this->analyst->id,
            'trader_id' => $this->trader->id,
        ]);

        $message1 = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $this->analyst->id,
            'content' => 'Message 1',
        ]);

        $response = $this->actingAs($this->trader)
            ->get(route('messages.poll', ['conversation' => $conversation, 'last_id' => 0]));

        $response->assertOk()
            ->assertJsonCount(1, 'messages')
            ->assertJsonFragment(['content' => 'Message 1']);
            
        // Test polling with offset
        $message2 = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $this->analyst->id,
            'content' => 'Message 2',
        ]);
        
        $response = $this->actingAs($this->trader)
             ->get(route('messages.poll', ['conversation' => $conversation, 'last_id' => $message1->id]));
             
        $response->assertOk()
            ->assertJsonCount(1, 'messages')
            ->assertJsonFragment(['content' => 'Message 2']);
            
        // Check read status update
        $this->assertDatabaseHas('messages', [
            'id' => $message2->id,
            'is_read' => true,
        ]);
    }
}
