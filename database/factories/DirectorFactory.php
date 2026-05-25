<?php

namespace Database\Factories;

use App\Models\Director;
use Illuminate\Database\Eloquent\Factories\Factory;

class DirectorFactory extends Factory
{
    protected $model = Director::class;

    public function definition(): array
    {
        return [
            // Inventa un nombre falso 
            'name' => $this->faker->name(),
            'surname' => $this->faker->lastName(),
            'birthdate' => $this->faker->date(),
            
        ];
    }
}