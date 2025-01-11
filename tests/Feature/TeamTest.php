<?php

namespace Feature;


use App\Models\Team;
use App\Models\User;
use Tests\TestCase;

class TeamTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user);
    }

    public function test_user_can_create_team()
    {
        $response = $this->postJson('/api/teams', [
            'name' => 'Test Team'
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['id', 'name', 'captain_id']);

        $this->assertDatabaseHas('teams', [
            'name'       => 'Test Team',
            'captain_id' => $this->user->id
        ]);
    }

    public function test_captain_can_remove_team_member()
    {
        $team = Team::factory()->create([
            'captain_id' => $this->user->id
        ]);

        $member = User::factory()->create();
        $team->members()->attach($member->id);

        $response = $this->postJson("/api/teams/{$team->id}/members/{$member->id}/remove");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('team_user', [
            'team_id' => $team->id,
            'user_id' => $member->id
        ]);
    }
}
