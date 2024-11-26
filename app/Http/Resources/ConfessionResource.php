<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConfessionResource extends JsonResource
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
            'message' => $this->message,
            'category' => $this->category,
            'emotion' => $this->emotion,
            'is_approved' => $this->is_approved,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
