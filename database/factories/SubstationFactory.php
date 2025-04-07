<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Substation>
 */
class SubstationFactory extends Factory
{
    protected $model = Substation::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company . ' Substation',
            'code' => 'S' . $this->faker->unique()->numberBetween(1000, 9999),
            'latitude' => $this->faker->latitude(3, 6),
            'longitude' => $this->faker->longitude(100, 103),
            'location_address' => $this->faker->address,
            'status' => $this->faker->randomElement(['Active', 'Inactive', 'Maintenance']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
