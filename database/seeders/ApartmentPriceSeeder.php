<?php

namespace Database\Seeders;

use App\Models\ApartmentPrice;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ApartmentPriceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ApartmentPrice::create([
            'apartment_id' => 1,
            'start_date' => now(),
            'end_date' => now()->addDays(7),
            'price' => 100
        ]);

        ApartmentPrice::create([
            'apartment_id' => 1,
            'start_date' => now()->addDays(8),
            'end_date' => now()->addDays(15),
            'price' => 90
        ]);
    }
}
