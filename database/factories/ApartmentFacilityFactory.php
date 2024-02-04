<?php

namespace Database\Factories;

use App\Models\Apartment;
use App\Models\Facility;
use App\Models\Property;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ApartmentFacility>
 */
class ApartmentFacilityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'apartment_id' => $this->faker->randomElement(Apartment::all()->pluck('id')),
            'facility_id' => $this->faker->randomElement(Facility::all()->pluck('id')),
        ];
    }
}
