<?php

namespace Database\Factories;

use App\Models\Especialidad;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Area>
 */
class AreaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $random_especialidad = Especialidad::inRandomOrder()->first();

        return [
            'nombre' => $this->faker->word,
            'descripcion' => $this->faker->sentence,
            'especialidad_id' => $random_especialidad->id ?? Especialidad::factory(),
        ];
    }
}
