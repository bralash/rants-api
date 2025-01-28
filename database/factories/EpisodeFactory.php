<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Episode>
 */
class EpisodeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'img_url' => $this->faker->imageUrl(),
            'audio_url' => $this->faker->url(),
            'duration' => $this->faker->time($format = 'H:i:s', $max = 'now'),
            'posted_on' => $this->faker->date(),
            'season' => $this->faker->numberBetween(1, 10),
            'episode' => $this->faker->numberBetween(1, 20),
            'spotify_url' => $this->faker->url(),
            'apple_podcasts_url' => $this->faker->optional()->url(),
            'archive' => '0',
            'featured' => '0',
            'slug' => Str::slug($this->faker->sentence())
        ];
    }
}
