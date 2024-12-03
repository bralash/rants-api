<?php

namespace App\Services;

use App\Models\Episode;
use App\Models\Playlist;

class PlaylistService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Create a new playlist with optional episodes.
     *
     * @param array $data
     * @return Playlist
     */
    public function createPlaylist(array $data): Playlist {
        $playlist = Playlist::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null
        ]);

        if(!empty($data['episodes'])) {
            $playlist->episodes->attach($data['episodes']);
        }

        return $playlist;
    }

    /**
     * Add episodes to an existing playlist.
     *
     * @param Playlist $playlist
     * @param array $episodeIds
     * @return void
     */
    public function addEpisodesToPlaylist(Playlist $playlist, array $episodeIds): void {
        $playlist->episodes()->syncWithoutDetaching($episodeIds);
    }

    /**
     * Remove an episode from a playlist.
     *
     * @param Playlist $playlist
     * @param int $episodeId
     * @return void
     */
    public function removeEpisodesFromPlaylist(Playlist $playlist, int $episodeId): void {
        $playlist->episodes()->detach($episodeId);
    }

    /**
     * Retrieve a playlist with its episodes.
     *
     * @param int $playlistId
     * @return Playlist
     */
    public function getPlaylistWithEpisodes(int $playlistId): Playlist {
        return Playlist::with('episodes')->findOrFail($playlistId);
    }

    public function updatePlaylist(Playlist $playlist, array $data): Playlist
    {
        $playlist->update([
            'name' => $data['name'] ?? $playlist->name,
            'description' => $data['description'] ?? $playlist->description,
        ]);

        if (isset($data['episodes'])) {
            $playlist->episodes()->sync($data['episodes']);
        }

        return $playlist;
    }

    public function deletePlaylist(Playlist $playlist): void
    {
        $playlist->delete();
    }
}
