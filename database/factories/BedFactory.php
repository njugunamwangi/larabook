<?php

namespace Database\Factories;

use App\Models\BedType;
use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Bed>
 */
class BedFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'room_id' => $this->faker->randomElement(Room::all()->pluck('id')),
            'bed_type_id' => $this->faker->randomElement(BedType::all()->pluck('id')),
        ];
    }
}
