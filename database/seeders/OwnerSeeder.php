<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OwnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Owner',
            'email' => 'owner@booking.com',
            'password' => bcrypt('ownerPassword'),
            'email_verified_at' => now(),
            'role_id' => Role::ROLE_OWNER
        ]);
    }
}
