<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EpisodeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'img_url' => $this->img_url,
            'audio_url' => $this->audio_url,
            'duration' => $this->duration,
            'posted_on' => $this->posted_on,
            'season' => $this->season,
            'episode' => $this->episode,
            'spotify_url' => $this->spotify_url,
            'apple_podcasts_url' => $this->apple_podcasts_url,
            'archive' => $this->archive,
            'featured' => $this->featured,
            'slug' => $this->slug,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
