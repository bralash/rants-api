<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEpisodeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $episode = $this->route('episode');

        return [
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string|max:1000',
            'img_url' => 'sometimes|url',
            'audio_url' => 'sometimes|url',
            'duration' => 'sometimes|string|max:20',
            'posted_on' => 'sometimes|date',
            'season' => 'sometimes|integer|min:1',
            'episode' => 'sometimes|integer|min:1',
            'spotify_url' => 'sometimes|url',
            'apple_podcasts_url' => 'nullable|url',
            'archive' => 'sometimes|in:0,1',
            'featured' => 'sometimes|in:0,1',
            'slug' => 'required|string|unique:episodes,slug,' . $episode->id, 
        ];
    }
}
