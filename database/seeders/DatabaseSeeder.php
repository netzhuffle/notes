<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\User::factory()
            ->hasNotes(10)
            ->create();
        \App\Models\User::factory()
            ->create();
        \App\Models\User::factory()
            ->hasNotes(2000)
            ->create();
    }
}
