<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PropertiesTest extends TestCase
{
    use RefreshDatabase;

    public function test_property_owner_has_access_to_properties_feature()
    {
        $this->artisan('migrate:seed --fresh');

        $owner = User::factory()->create()->assignRole(Role::ROLE_OWNER);
        $response = $this->actingAs($owner)->getJson('/api/owner/properties');

        $response->assertStatus(200);
    }

    public function test_user_does_not_have_access_to_properties_feature()
    {
        $this->artisan('migrate:seed --fresh');

        $owner = User::factory()->create()->assignRole(Role::ROLE_USER);
        $response = $this->actingAs($owner)->getJson('/api/owner/properties');

        $response->assertStatus(403);
    }
}
