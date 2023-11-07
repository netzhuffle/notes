<?php

namespace Tests\Feature;

use App\Models\Note;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Integration tests for REST DELETE a note. */
class NoteDeleteTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthorised_returns_401(): void
    {
        $user = User::factory()->create();
        $note = Note::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->deleteJson("/api/v1/notes/$note->id");

        $response->assertStatus(401);
        $this->assertDatabaseHas('notes', [
            'id' => $note->id,
        ]);
    }

    public function test_unauthorised_not_existing_returns_401(): void
    {
        $response = $this->deleteJson('/api/v1/notes/1');

        $response->assertStatus(401);
    }

    public function test_not_existing_returns_404(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->deleteJson('/api/v1/notes/1');

        $response->assertStatus(404);
    }

    public function test_success_returns_204(): void
    {
        $user = User::factory()->create();
        $note = Note::factory()->create([
            'user_id' => $user->id,
        ]);
        $noteId = $note->id;

        $response = $this->actingAs($user)->deleteJson("/api/v1/notes/$noteId");

        $response
            ->assertStatus(204);
        $this->assertDatabaseMissing('notes', [
            'id' => $noteId,
        ]);
    }

    public function test_other_users_note_returns_404(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $otherUsersNote = Note::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        $response = $this->actingAs($user)->deleteJson("/api/v1/notes/$otherUsersNote->id");

        $response->assertStatus(404);
        $this->assertDatabaseHas('notes', [
            'id' => $otherUsersNote->id,
        ]);
    }
}
