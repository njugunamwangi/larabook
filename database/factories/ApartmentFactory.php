<?php

namespace Database\Factories;

use App\Models\ApartmentType;
use App\Models\Property;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Apartment>
 */
class ApartmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'property_id' => $this->faker->randomElement(Property::all()->pluck('id')),
            'apartment_type_id' => $this->faker->randomElement(ApartmentType::all()->pluck('id')),
            'name' => fake()->text(20),
            'capacity_adults' => rand(1, 5),
            'capacity_children' => rand(1, 5),
            'size' => rand(10, 50),
        ];
    }
}
