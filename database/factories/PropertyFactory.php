<?php

namespace Database\Factories;

use App\Models\City;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Property>
 */
class PropertyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'owner_id' => User::role(Role::ROLE_OWNER)->value('id'),
            'name' => $this->faker->name(20),
            'city_id' => City::value('id'),
            'address_street' => $this->faker->streetAddress(),
            'address_postcode' => $this->faker->postcode(),
            'lat' => $this->faker->latitude(),
            'long' => $this->faker->longitude(),
        ];
    }
}
