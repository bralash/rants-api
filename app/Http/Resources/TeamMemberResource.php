<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeamMemberResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'role' => $this->role,
            'bio' => $this->bio,
            'profile_image' => $this->profile_image,
            'social_media_links' => SocialMediaLinkResource::collection($this->whenLoaded('socialMediaLinks')),
            'created-at' => $this->created_at->toISOString(),
            'updated-at' => $this->updated_at->toISOString()
        ];
    }
}
