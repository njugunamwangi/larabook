<?php

namespace Tests\Feature;

use App\Models\Apartment;
use App\Models\Bed;
use App\Models\BedType;
use App\Models\City;
use App\Models\Country;
use App\Models\Facility;
use App\Models\GeoObject;
use App\Models\Property;
use App\Models\Role;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PropertySearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_property_search_by_city_returns_correct_results(): void
    {

        $owner = User::factory()->create()->assignRole(Role::ROLE_OWNER);
        $cities = City::take(2)->pluck('id');
        $propertyInCity = Property::factory()->create(['owner_id' => $owner->id, 'city_id' => $cities[0]]);
        $propertyInAnotherCity = Property::factory()->create(['owner_id' => $owner->id, 'city_id' => $cities[1]]);

        $response = $this->getJson('/api/search?city=' . $cities[0]);

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'properties');
        $response->assertJsonFragment(['id' => $propertyInCity->id]);
    }

    public function test_property_search_by_country_returns_correct_results(): void
    {

        $owner = User::factory()->create()->assignRole(Role::ROLE_OWNER);
        $countries = Country::with('cities')->take(2)->get();
        $propertyInCountry = Property::factory()->create([
            'owner_id' => $owner->id,
            'city_id' => $countries[0]->cities()->value('id')
        ]);
        $propertyInAnotherCountry = Property::factory()->create([
            'owner_id' => $owner->id,
            'city_id' => $countries[1]->cities()->value('id')
        ]);

        $response = $this->getJson('/api/search?country=' . $countries[0]->id);

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'properties');
        $response->assertJsonFragment(['id' => $propertyInCountry->id]);
    }

    public function test_property_search_by_geo_object_returns_correct_results(): void
    {

        $owner = User::factory()->create()->assignRole(Role::ROLE_OWNER);
        $cityId = City::value('id');
        $geo_object = GeoObject::first();
        $propertyNear = Property::factory()->create([
            'owner_id' => $owner->id,
            'city_id' => $cityId,
            'lat' => $geo_object->lat,
            'long' => $geo_object->long,
        ]);
        $propertyFar = Property::factory()->create([
            'owner_id' => $owner->id,
            'city_id' => $cityId,
            'lat' => $geo_object->lat + 10,
            'long' => $geo_object->long - 10,
        ]);

        $response = $this->getJson('/api/search?geo_object=' . $geo_object->id);

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'properties');
        $response->assertJsonFragment(['id' => $propertyNear->id]);
    }

    public function test_property_search_by_capacity_returns_correct_results(): void
    {
        $owner = User::factory()->create()->assignRole(Role::ROLE_OWNER);
        $cityId = City::value('id');
        $propertyWithSmallApartment = Property::factory()->create([
            'owner_id' => $owner->id,
            'city_id' => $cityId,
        ]);
        Apartment::factory()->create([
            'property_id' => $propertyWithSmallApartment->id,
            'capacity_adults' => 1,
            'capacity_children' => 0,
        ]);
        $propertyWithLargeApartment = Property::factory()->create([
            'owner_id' => $owner->id,
            'city_id' => $cityId,
        ]);
        Apartment::factory()->create([
            'property_id' => $propertyWithLargeApartment->id,
            'capacity_adults' => 3,
            'capacity_children' => 2,
        ]);

        $response = $this->getJson('/api/search?city=' . $cityId . '&adults=2&children=1');

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'properties');
        $response->assertJsonFragment(['id' => $propertyWithLargeApartment->id]);
    }

    public function test_property_search_by_capacity_returns_only_suitable_apartments(): void
    {
        $owner = User::factory()->create()->assignRole(Role::ROLE_OWNER);
        $cityId = City::value('id');
        $property = Property::factory()->create([
            'owner_id' => $owner->id,
            'city_id' => $cityId,
        ]);
        $smallApartment = Apartment::factory()->create([
            'name' => 'Small apartment',
            'property_id' => $property->id,
            'capacity_adults' => 1,
            'capacity_children' => 0,
        ]);
        $largeApartment = Apartment::factory()->create([
            'name' => 'Large apartment',
            'property_id' => $property->id,
            'capacity_adults' => 3,
            'capacity_children' => 2,
        ]);

        $response = $this->getJson('/api/search?city=' . $cityId . '&adults=2&children=1');

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'properties');
        $response->assertJsonCount(1, 'properties.0.apartments');
        $response->assertJsonPath('properties.0.apartments.0.name', $largeApartment->name);
    }

    public function test_property_search_beds_list_all_cases(): void
    {
        $owner = User::factory()->create()->assignRole(Role::ROLE_OWNER);
        $cityId = City::value('id');
        $roomTypes = RoomType::all();
        $bedTypes = BedType::all();

        $property = Property::factory()->create([
            'owner_id' => $owner->id,
            'city_id' => $cityId,
        ]);
        $apartment = Apartment::factory()->create([
            'name' => 'Small apartment',
            'property_id' => $property->id,
            'capacity_adults' => 1,
            'capacity_children' => 0,
        ]);

        // ----------------------
        // FIRST: check that bed list if empty if no beds
        // ----------------------

        $response = $this->getJson('/api/search?city=' . $cityId);
        $response->assertStatus(200);
        $response->assertJsonCount(1, 'properties');
        $response->assertJsonCount(1, 'properties.0.apartments');
        $response->assertJsonPath('properties.0.apartments.0.beds_list', '');

        // ----------------------
        // SECOND: create 1 room with 1 bed
        // ----------------------

        $room = Room::create([
            'apartment_id' => $apartment->id,
            'room_type_id' => $roomTypes[0]->id,
            'name' => 'Bedroom',
        ]);
        Bed::create([
            'room_id' => $room->id,
            'bed_type_id' => $bedTypes[0]->id,
        ]);

        $response = $this->getJson('/api/search?city=' . $cityId);
        $response->assertStatus(200);
        $response->assertJsonPath('properties.0.apartments.0.beds_list', '1 ' . $bedTypes[0]->name);

        // ----------------------
        // THIRD: add another bed to the same room
        // ----------------------

        Bed::create([
            'room_id' => $room->id,
            'bed_type_id' => $bedTypes[0]->id,
        ]);
        $response = $this->getJson('/api/search?city=' . $cityId);
        $response->assertStatus(200);
        $response->assertJsonPath('properties.0.apartments.0.beds_list', '2 ' . str($bedTypes[0]->name)->plural());

        // ----------------------
        // FOURTH: add a second room with no beds
        // ----------------------

        $secondRoom = Room::create([
            'apartment_id' => $apartment->id,
            'room_type_id' => $roomTypes[0]->id,
            'name' => 'Living room',
        ]);
        $response = $this->getJson('/api/search?city=' . $cityId);
        $response->assertStatus(200);
        $response->assertJsonPath('properties.0.apartments.0.beds_list', '2 ' . str($bedTypes[0]->name)->plural());

        // ----------------------
        // FIFTH: add one bed to that second room
        // ----------------------

        Bed::create([
            'room_id' => $secondRoom->id,
            'bed_type_id' => $bedTypes[0]->id,
        ]);
        $response = $this->getJson('/api/search?city=' . $cityId);
        $response->assertStatus(200);
        $response->assertJsonPath('properties.0.apartments.0.beds_list', '3 ' . str($bedTypes[0]->name)->plural());

        // ----------------------
        // SIXTH: add another bed with a different type to that second room
        // ----------------------

        Bed::create([
            'room_id' => $secondRoom->id,
            'bed_type_id' => $bedTypes[1]->id,
        ]);
        $response = $this->getJson('/api/search?city=' . $cityId);
        $response->assertStatus(200);
        $response->assertJsonPath('properties.0.apartments.0.beds_list', '4 beds (3 ' . str($bedTypes[0]->name)->plural() . ', 1 ' . $bedTypes[1]->name . ')');

        // ----------------------
        // SEVENTH: add a second bed with that new type to that second room
        // ----------------------

        Bed::create([
            'room_id' => $secondRoom->id,
            'bed_type_id' => $bedTypes[1]->id,
        ]);
        $response = $this->getJson('/api/search?city=' . $cityId);
        $response->assertStatus(200);
        $response->assertJsonPath('properties.0.apartments.0.beds_list', '5 beds (3 ' . str($bedTypes[0]->name)->plural() . ', 2 ' . str($bedTypes[1]->name)->plural() . ')');
    }

    public function test_property_search_returns_one_best_apartment_per_property()
    {
        $owner = User::factory()->create()->assignRole(Role::ROLE_OWNER);
        $cityId = City::value('id');
        $property = Property::factory()->create([
            'owner_id' => $owner->id,
            'city_id' => $cityId,
        ]);
        $largeApartment = Apartment::factory()->create([
            'name' => 'Large apartment',
            'property_id' => $property->id,
            'capacity_adults' => 3,
            'capacity_children' => 2,
        ]);
        $midSizeApartment = Apartment::factory()->create([
            'name' => 'Mid size apartment',
            'property_id' => $property->id,
            'capacity_adults' => 2,
            'capacity_children' => 1,
        ]);
        $smallApartment = Apartment::factory()->create([
            'name' => 'Small apartment',
            'property_id' => $property->id,
            'capacity_adults' => 1,
            'capacity_children' => 0,
        ]);

        $property2 = Property::factory()->create([
            'owner_id' => $owner->id,
            'city_id' => $cityId,
        ]);
        Apartment::factory()->create([
            'name' => 'Large apartment 2',
            'property_id' => $property2->id,
            'capacity_adults' => 3,
            'capacity_children' => 2,
        ]);
        Apartment::factory()->create([
            'name' => 'Mid size apartment 2',
            'property_id' => $property2->id,
            'capacity_adults' => 2,
            'capacity_children' => 1,
        ]);
        Apartment::factory()->create([
            'name' => 'Small apartment 2',
            'property_id' => $property2->id,
            'capacity_adults' => 1,
            'capacity_children' => 0,
        ]);

        $response = $this->getJson('/api/search?city=' . $cityId . '&adults=2&children=1');

        $response->assertStatus(200);
        $response->assertJsonCount(2,'properties');
        $response->assertJsonCount(1, 'properties.0.apartments');
        $response->assertJsonCount(1, 'properties.1.apartments');
        $response->assertJsonPath('properties.0.apartments.0.name', $midSizeApartment->name);
    }

    public function test_property_search_filters_by_facilities()
    {
        $owner = User::factory()->create()->assignRole(Role::ROLE_OWNER);
        $cityId = City::value('id');
        $property = Property::factory()->create([
            'owner_id' => $owner->id,
            'city_id' => $cityId,
        ]);
        Apartment::factory()->create([
            'name' => 'Mid size apartment',
            'property_id' => $property->id,
            'capacity_adults' => 2,
            'capacity_children' => 1,
        ]);
        $property2 = Property::factory()->create([
            'owner_id' => $owner->id,
            'city_id' => $cityId,
        ]);
        Apartment::factory()->create([
            'name' => 'Mid size apartment',
            'property_id' => $property2->id,
            'capacity_adults' => 2,
            'capacity_children' => 1,
        ]);

        // First case - no facilities exist
        $response = $this->getJson('/api/search?city=' . $cityId . '&adults=2&children=1');
        $response->assertStatus(200);
        $response->assertJsonCount(2, 'properties');

        // Second case - filter by facility, 0 properties returned
        $facility = Facility::create(['name' => 'First facility']);
        $response = $this->getJson('/api/search?city=' . $cityId . '&adults=2&children=1&facilities[]=' . $facility->id);
        $response->assertStatus(200);
        $response->assertJsonCount(0, 'properties');

        // Third case - attach facility to property, filter by facility, 1 property returned
        $property->facilities()->attach($facility->id);
        $response = $this->getJson('/api/search?city=' . $cityId . '&adults=2&children=1&facilities[]=' . $facility->id);
        $response->assertStatus(200);
        $response->assertJsonCount(1, 'properties');

        // Fourth case - attach facility to a DIFFERENT property, filter by facility, 2 properties returned
        $property2->facilities()->attach($facility->id);
        $response = $this->getJson('/api/search?city=' . $cityId . '&adults=2&children=1&facilities[]=' . $facility->id);
        $response->assertStatus(200);
        $response->assertJsonCount(2, 'properties');
    }

    public function test_property_search_filters_by_price()
    {
        $owner = User::factory()->create()->assignRole(Role::ROLE_OWNER);
        $cityId = City::value('id');
        $property = Property::factory()->create([
            'owner_id' => $owner->id,
            'city_id' => $cityId,
        ]);
        $cheapApartment = Apartment::factory()->create([
            'name' => 'Cheap apartment',
            'property_id' => $property->id,
            'capacity_adults' => 2,
            'capacity_children' => 1,
        ]);
        $cheapApartment->prices()->create([
            'start_date' => now(),
            'end_date' => now()->addMonth(),
            'price' => 70,
        ]);
        $property2 = Property::factory()->create([
            'owner_id' => $owner->id,
            'city_id' => $cityId,
        ]);
        $expensiveApartment = Apartment::factory()->create([
            'name' => 'Mid size apartment',
            'property_id' => $property2->id,
            'capacity_adults' => 2,
            'capacity_children' => 1,
        ]);
        $expensiveApartment->prices()->create([
            'start_date' => now(),
            'end_date' => now()->addMonth(),
            'price' => 130,
        ]);

        // First case - no price range: both returned
        $response = $this->getJson('/api/search?city=' . $cityId . '&adults=2&children=1');
        $response->assertStatus(200);
        $response->assertJsonCount(2, 'properties');

        // First case - min price set: 1 returned
        $response = $this->getJson('/api/search?city=' . $cityId . '&adults=2&children=1&price_from=100');
        $response->assertStatus(200);
        $response->assertJsonCount(1, 'properties');

        // Second case - max price set: 1 returned
        $response = $this->getJson('/api/search?city=' . $cityId . '&adults=2&children=1&price_to=100');
        $response->assertStatus(200);
        $response->assertJsonCount(1, 'properties');

        // Third case - both min and max price set: 2 returned
        $response = $this->getJson('/api/search?city=' . $cityId . '&adults=2&children=1&price_from=50&price_to=150');
        $response->assertStatus(200);
        $response->assertJsonCount(2, 'properties');

        // Fourth case - both min and max price set narrow: 0 returned
        $response = $this->getJson('/api/search?city=' . $cityId . '&adults=2&children=1&price_from=80&price_to=100');
        $response->assertStatus(200);
        $response->assertJsonCount(0, 'properties');
    }
}
