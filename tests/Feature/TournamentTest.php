<?php

namespace Feature;


use App\Models\Tournament;
use App\Models\User;
use Tests\TestCase;

class TournamentTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['balance' => 1000]);
        Sanctum::actingAs($this->user);
    }

    public function test_user_can_create_tournament()
    {
        $response = $this->postJson('/api/tournaments', [
            'name'        => 'Test Tournament',
            'description' => 'Test Description',
            'entry_fee'   => 100,
            'type'        => 'individual',
            'start_date'  => now()->addDays(1),
            'end_date'    => now()->addDays(2)
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['id', 'name', 'entry_fee']);

        $this->assertDatabaseHas('tournaments', [
            'name'       => 'Test Tournament',
            'creator_id' => $this->user->id
        ]);
    }

    public function test_user_can_join_tournament()
    {
        $tournament = Tournament::factory()->create([
            'entry_fee' => 100
        ]);

        $response = $this->postJson("/api/tournaments/{$tournament->id}/join");

        $response->assertStatus(200);

        $this->assertDatabaseHas('tournament_participants', [
            'tournament_id'    => $tournament->id,
            'participant_id'   => $this->user->id,
            'participant_type' => 'User',
            'payment_status'   => true
        ]);

        $this->assertEquals(900, $this->user->fresh()->balance);
    }
}
