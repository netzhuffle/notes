<?php

namespace Tests\Feature;

use App\Models\Note;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

/** Integration tests for REST PUT a note. */
class NotePutTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthorised_returns_401(): void
    {
        $noteTitle = 'Test note';
        $noteContent = 'Test note content';
        $noteNewTitle = 'New title';
        $user = User::factory()->create();
        $note = Note::create([
            'user_id' => $user->id,
            'title' => $noteTitle,
            'content' => $noteContent,
        ]);

        $response = $this->putJson("/api/v1/notes/$note->id", [
            'title' => $noteNewTitle,
            'content' => 'New content',
        ]);

        $response->assertStatus(401);
        $this->assertDatabaseHas('notes', [
            'title' => $noteTitle,
            'content' => $noteContent,
        ]);
        $this->assertDatabaseMissing('notes', [
            'title' => $noteNewTitle,
        ]);
    }

    public function test_unauthorised_not_existing_returns_401(): void
    {
        $noteTitle = 'Test note';
        $response = $this->putJson('/api/v1/notes/1', [
            'title' => $noteTitle,
            'content' => 'Test note content',
        ]);

        $response->assertStatus(401);
        $this->assertDatabaseMissing('notes', [
            'title' => $noteTitle,
        ]);
    }

    public function test_not_existing_returns_404(): void
    {
        $noteNewTitle = 'New title';
        $user = User::factory()->create();

        $response = $this->actingAs($user)->putJson("/api/v1/notes/1", [
            'title' => $noteNewTitle,
            'content' => 'New content',
        ]);

        $response->assertStatus(404);
        $this->assertDatabaseMissing('notes', [
            'title' => $noteNewTitle,
        ]);
    }

    public function test_full_note_returns_200_and_note(): void
    {
        $noteTitle = 'Test note';
        $noteContent = 'Test note content';
        $noteNewTitle = 'New title';
        $noteNewContent = 'New content';
        $user = User::factory()->create();
        $note = Note::create([
            'user_id' => $user->id,
            'title' => $noteTitle,
            'content' => $noteContent,
        ]);
        $noteId = $note->id;

        $response = $this->actingAs($user)->putJson("/api/v1/notes/$noteId", [
            'title' => $noteNewTitle,
            'content' => $noteNewContent,
        ]);

        $response
            ->assertStatus(200)
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('id', $noteId)
                ->where('title', $noteNewTitle)
                ->where('content', $noteNewContent)
                ->etc()
            );
        $this->assertDatabaseHas('notes', [
            'id' => $noteId,
            'user_id' => $user->id,
            'title' => $noteNewTitle,
            'content' => $noteNewContent,
        ]);
        $this->assertDatabaseMissing('notes', [
            'title' => $noteTitle,
        ]);
    }

    public function test_title_only_clears_content_and_returns_200_and_note(): void
    {
        $noteTitle = 'Test note';
        $noteContent = 'Test note content';
        $noteNewTitle = 'New title';
        $user = User::factory()->create();
        $note = Note::create([
            'user_id' => $user->id,
            'title' => $noteTitle,
            'content' => $noteContent,
        ]);
        $noteId = $note->id;

        $response = $this->actingAs($user)->putJson("/api/v1/notes/$noteId", [
            'title' => $noteNewTitle,
        ]);

        $response
            ->assertStatus(200)
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('id', $noteId)
                ->where('title', $noteNewTitle)
                ->where('content', null)
                ->etc()
            );
        $this->assertDatabaseHas('notes', [
            'id' => $noteId,
            'user_id' => $user->id,
            'title' => $noteNewTitle,
            'content' => null,
        ]);
    }

    public function test_content_only_returns_422_and_message(): void
    {
        $noteTitle = 'Test note';
        $noteContent = 'Test note content';
        $noteNewContent = 'New content';
        $user = User::factory()->create();
        $note = Note::create([
            'user_id' => $user->id,
            'title' => $noteTitle,
            'content' => $noteContent,
        ]);
        $noteId = $note->id;

        $response = $this->actingAs($user)->putJson("/api/v1/notes/$noteId", [
            'content' => $noteNewContent,
        ]);

        $response
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The title field is required.',
            ]);
        $this->assertDatabaseHas('notes', [
            'id' => $noteId,
            'user_id' => $user->id,
            'title' => $noteTitle,
            'content' => $noteContent,
        ]);
        $this->assertDatabaseMissing('notes', [
            'content' => $noteNewContent,
        ]);
    }

    public function test_title_too_long_returns_422_and_message(): void
    {
        $noteTitle = 'Test note';
        $noteContent = 'Test note content';
        $noteNewTitle = str_repeat('a', 256);
        $user = User::factory()->create();
        $note = Note::create([
            'user_id' => $user->id,
            'title' => $noteTitle,
            'content' => $noteContent,
        ]);
        $noteId = $note->id;

        $response = $this->actingAs($user)->putJson("/api/v1/notes/$noteId", [
            'title' => $noteNewTitle,
        ]);

        $response
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The title field must not be greater than 255 characters.',
            ]);
        $this->assertDatabaseHas('notes', [
            'id' => $noteId,
            'user_id' => $user->id,
            'title' => $noteTitle,
            'content' => $noteContent,
        ]);
        $this->assertDatabaseMissing('notes', [
            'title' => $noteNewTitle,
        ]);
    }
}
