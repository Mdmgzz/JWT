<?php

namespace Database\Factories;

use App\Models\Director;
use App\Models\Film;
use Illuminate\Database\Eloquent\Factories\Factory;

class FilmFactory extends Factory
{
    protected $model = Film::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'release_date' => $this->faker->date(),
            'sinopsis' => $this->faker->paragraph(),
            'duration' => $this->faker->numberBetween(60, 240),
            'gendre' => $this->faker->word(),
            'director_id' => Director::factory(), // Crea un director automáticamente si no le pasas uno
        ];
    }
}
