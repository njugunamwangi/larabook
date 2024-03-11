<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::create([
            'name' => 'User',
            'email' => 'user@booking.com',
            'password' => bcrypt('Password'),
            'email_verified_at' => now(),
        ]);

        $user->assignRole(Role::ROLE_USER);
    }
}
