<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Episode extends Model
{
    /** @use HasFactory<\Database\Factories\EpisodeFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'img_url',
        'audio_url',
        'duration',
        'posted_on',
        'season',
        'episode',
        'spotify_url',
        'apple_podcasts_url',
        'archive',
        'featured',
        'slug'
    ];

    public function playlists() {
        return $this->belongsToMany(Playlist::class, 'playlist_episode')->withTimestamps();
    }
}
