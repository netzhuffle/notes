<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class NotePostTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthorised_returns_401(): void
    {
        $noteTitle = 'Test note';
        $noteContent = 'Test note content';
        $response = $this->postJson('/api/v1/notes', [
            'title' => $noteTitle,
            'content' => $noteContent,
        ]);

        $response->assertStatus(401);
        $this->assertDatabaseMissing('notes', [
            'title' => $noteTitle,
            'content' => $noteContent,
        ]);
    }

    public function test_full_note_returns_201_and_note(): void
    {
        $noteTitle = 'Test note';
        $noteContent = 'Test note content';
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/v1/notes', [
            'title' => $noteTitle,
            'content' => $noteContent,
        ]);

        $response
            ->assertStatus(201)
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('title', $noteTitle)
                ->where('content', $noteContent)
                ->etc()
            );
        $this->assertDatabaseHas('notes', [
            'user_id' => $user->id,
            'title' => $noteTitle,
            'content' => $noteContent,
        ]);
    }

    public function test_title_only_returns_201_and_note(): void
    {
        $noteTitle = 'Test note';
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/v1/notes', [
            'title' => $noteTitle,
        ]);

        $response
            ->assertStatus(201)
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('title', $noteTitle)
                ->where('content', null)
                ->etc()
            );
        $this->assertDatabaseHas('notes', [
            'user_id' => $user->id,
            'title' => $noteTitle,
            'content' => null,
        ]);
    }

    public function test_content_only_returns_422_and_message(): void
    {
        $noteContent = 'Test note content';
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/v1/notes', [
            'content' => $noteContent,
        ]);

        $response
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The title field is required.',
            ]);
        $this->assertDatabaseMissing('notes', [
            'content' => $noteContent,
        ]);
    }

    public function test_title_too_long_returns_422_and_message(): void
    {
        $noteTitle = str_repeat('a', 256);
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/v1/notes', [
            'title' => $noteTitle,
        ]);

        $response
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The title field must not be greater than 255 characters.',
            ]);
        $this->assertDatabaseMissing('notes', [
            'title' => $noteTitle,
        ]);
    }
}
