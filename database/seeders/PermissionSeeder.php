<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $allRoles = Role::all()->keyBy('id');

        $permissions = [
            'properties-manage' => [Role::ROLE_OWNER],
            'bookings-manage' => [Role::ROLE_USER],
        ];

        foreach ($permissions as $key => $roles) {
            $permission = Permission::create(['name' => $key]);
            foreach ($roles as $role) {
                $allRoles[$role]->givePermissionTo($permission);
            }
        }
    }
}
