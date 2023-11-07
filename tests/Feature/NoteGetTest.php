<?php

namespace Tests\Feature;

use App\Models\Note;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NoteGetTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthorised_returns_401(): void
    {
        $user = User::factory()->create();
        $note = Note::create([
            'user_id' => $user->id,
            'title' => 'Test note',
            'content' => 'Test note content',
        ]);

        $response = $this->getJson("/api/v1/notes/$note->id");

        $response->assertStatus(401);
    }

    public function test_unauthorised_not_existing_returns_401(): void
    {
        $response = $this->getJson('/api/v1/notes/1');

        $response->assertStatus(401);
    }

    public function test_not_existing_returns_404(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/api/v1/notes/1');

        $response->assertStatus(404);
    }

    public function test_success_returns_200_and_note(): void
    {
        $noteTitle = 'Test note';
        $noteContent = 'Test note content';
        $user = User::factory()->create();
        $note = Note::create([
            'user_id' => $user->id,
            'title' => $noteTitle,
            'content' => $noteContent,
        ]);
        $noteId = $note->id;

        $response = $this->actingAs($user)->getJson("/api/v1/notes/$noteId");

        $response
            ->assertStatus(200)
            ->assertJson([
                'id' => $noteId,
                'title' => $noteTitle,
                'content' => $noteContent,
            ]);
    }

    public function test_other_users_note_returns_404(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $otherUsersNote = Note::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        $response = $this->actingAs($user)->getJson("/api/v1/notes/$otherUsersNote->id");

        $response->assertStatus(404);
    }
}
