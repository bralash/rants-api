<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Playlist;
use App\Models\Episode;

class PlaylistEpisodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $playlists = Playlist::all();

        foreach ($playlists as $playlist) {
            // Attach random episodes to each playlist
            $episodes = Episode::inRandomOrder()->take(rand(3, 10))->pluck('id');
            $playlist->episodes()->attach($episodes);
        }
    }
}
