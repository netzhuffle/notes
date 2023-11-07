<?php

namespace Tests\Feature;

use App\Models\Note;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Integration tests for REST GET the list of notes. */
class NoteIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthorised_returns_401(): void
    {
        User::factory()->hasNotes(3)->create();

        $response = $this->getJson('/api/v1/notes');

        $response->assertStatus(401);
    }

    public function test_0_notes_returns_200_and_empty_list(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/api/v1/notes');

        $response
            ->assertStatus(200)
            ->assertJson([
                'data' => [],
                'total' => 0,
                'per_page' => 20,
                'current_page' => 1,
                'last_page' => 1,
                'from' => null,
                'to' => null,
            ]);
    }

    public function test_20_notes_returns_200_and_notes(): void
    {
        $user = User::factory()->create();
        $notes = [];
        for ($i = 0; $i < 20; $i++) {
            $notes[] = Note::factory()->create([
                'user_id' => $user->id,
            ]);
            $this->travel(1)->seconds();
        }

        $response = $this->actingAs($user)->getJson('/api/v1/notes');

        $notes = collect($notes)
            ->reverse()
            ->values()
            ->toArray();
        $response
            ->assertStatus(200)
            ->assertJson([
                'data' => $notes,
                'total' => 20,
                'per_page' => 20,
                'current_page' => 1,
                'last_page' => 1,
                'from' => 1,
                'to' => 20,
            ]);
    }

    public function test_21_notes_returns_200_and_first_20_notes(): void
    {
        $user = User::factory()->create();
        $notes = [];
        for ($i = 0; $i < 21; $i++) {
            $notes[] = Note::factory()->create([
                'user_id' => $user->id,
            ]);
            $this->travel(1)->seconds();
        }

        $response = $this->actingAs($user)->getJson('/api/v1/notes');

        $notes = collect($notes)
            ->reverse()
            ->values()
            ->slice(0, 20)
            ->toArray();
        $response
            ->assertStatus(200)
            ->assertJson([
                'data' => $notes,
                'total' => 21,
                'per_page' => 20,
                'current_page' => 1,
                'last_page' => 2,
                'from' => 1,
                'to' => 20,
            ]);
    }

    public function test_21_notes_page_2_returns_200_and_last_note(): void
    {
        $user = User::factory()->create();
        $notes = [];
        for ($i = 0; $i < 21; $i++) {
            $notes[] = Note::factory()->create([
                'user_id' => $user->id,
            ]);
            $this->travel(1)->seconds();
        }

        $response = $this->actingAs($user)->getJson('/api/v1/notes?page=2');

        $notes = collect([$notes[0]])->toArray();
        $response
            ->assertStatus(200)
            ->assertJson([
                'data' => $notes,
                'total' => 21,
                'per_page' => 20,
                'current_page' => 2,
                'last_page' => 2,
                'from' => 21,
                'to' => 21,
            ]);
    }
}
