<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Episode;
use App\Models\Playlist;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // Episode::factory()->count(10)->create();
        // Playlist::factory()->count(10)->create();

        $this->call([
            // PlaylistSeeder::class,
            PlaylistEpisodeSeeder::class,
        ]);
    }
}
