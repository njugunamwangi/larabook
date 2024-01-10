<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::create([
            'name' => 'Administrator',
            'email' => 'superadmin@booking.com',
            'password' => bcrypt('SuperSecretPassword'),
            'email_verified_at' => now(),
        ]);

        $user->assignRole(Role::ROLE_ADMINISTRATOR);
    }
}
