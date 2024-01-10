<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BasicUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'User',
            'email' => 'user@booking.com',
            'password' => bcrypt('userPassword'),
            'email_verified_at' => now(),
            'role_id' => Role::ROLE_USER
        ]);
    }
}
