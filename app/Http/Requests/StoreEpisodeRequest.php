<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEpisodeRequest extends FormRequest
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
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'img_url' => 'required|url',
            'audio_url' => 'required|url',
            'duration' => 'required|string',
            'posted_on' => 'required|date',
            'season' => 'required|integer|min:1',
            'episode' => 'required|integer|min:1',
            'spotify_url' => 'required|url',
            'apple_podcasts_url' => 'nullable|url',
            'archive' => 'nullable|in:0,1',
            'featured' => 'nullable|in:0,1',
            'slug' => 'required|string|unique:episodes,slug',
        ];
    }
}
