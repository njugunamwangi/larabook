<?php

namespace Database\Seeders;

use App\Models\ApartmentFacility;
use Database\Factories\ApartmentFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ApartmentFacilitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ApartmentFacility::factory(10)->create();
    }
}
