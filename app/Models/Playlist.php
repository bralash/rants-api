<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Playlist extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'description'];

    public function episodes() {
        return $this->belongsToMany(Episode::class, 'playlist_episode')->withTimestamps();
    }
}
