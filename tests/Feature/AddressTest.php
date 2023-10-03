<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Contact;
use Database\Seeders\AddressSeeder;
use Database\Seeders\ContactSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class AddressTest extends TestCase
{
    public function testCreateSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::query()->get()->first();
        $this->post(
            '/api/contacts/' . $contact->id . '/addresses',
            [
                'street' => 'jalan',
                'city' => 'kota',
                'province' => 'provinsi',
                'country' => 'negara',
                'postal_code' => '1111',
            ],
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(201)
            ->assertJson([
                "data" => [
                    'street' => 'jalan',
                    'city' => 'kota',
                    'province' => 'provinsi',
                    'country' => 'negara',
                    'postal_code' => '1111',
                ]
            ]);
    }

    public function testCreateFailed()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::query()->get()->first();
        $this->post(
            '/api/contacts/' . $contact->id . '/addresses',
            [
                'street' => 'jalan',
                'city' => 'kota',
                'province' => 'provinsi',
                'postal_code' => '1111',
            ],
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(400)
            ->assertJson([
                "errors" => [
                    'country' => [
                        "The country field is required."
                    ]
                ]
            ]);
    }

    public function testCreateNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::query()->get()->first();
        $this->post(
            '/api/contacts/' . $contact->id + 1 . '/addresses',
            [
                'street' => 'jalan',
                'city' => 'kota',
                'province' => 'provinsi',
                'country' => 'negara',
                'postal_code' => '1111',
            ],
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(404)
            ->assertJson([
                "errors" => [
                    'message' => [
                        "not found"
                    ]
                ]
            ]);
    }

    public function testListSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);

        $contact = Contact::query()->get()->first();
        $this->get(
            '/api/contacts/' . $contact->id . '/addresses',
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(200)
            ->assertJson([
                "data" => [
                    [
                        'street' => 'jalan',
                        'city' => 'kota',
                        'province' => 'provinsi',
                        'country' => 'negara',
                        'postal_code' => '1111',
                    ]
                ]
            ]);
    }

    public function testListNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);

        $contact = Contact::query()->get()->first();
        $this->get(
            '/api/contacts/' . $contact->id + 1 . '/addresses',
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(404)
            ->assertJson([
                "errors" => [
                    'message' => [
                        "not found"
                    ]
                ]
            ]);
    }

    public function testGetAddressSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->get()->first();
        $this->get(
            '/api/contacts/' . $address->contact_id . '/addresses/' . $address->id,
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(200)
            ->assertJson([
                "data" => [
                    'street' => 'jalan',
                    'city' => 'kota',
                    'province' => 'provinsi',
                    'country' => 'negara',
                    'postal_code' => '1111',
                ]
            ]);
    }

    public function testGetAddressNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->get()->first();
        $this->get(
            '/api/contacts/' . $address->contact_id . '/addresses/' . $address->id + 1,
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(404)
            ->assertJson([
                "errors" => [
                    'message' => [
                        "not found"
                    ]
                ]
            ]);
    }

    public function testUpdateAddressSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->get()->first();
        $this->put(
            '/api/contacts/' . $address->contact_id . '/addresses/' . $address->id,
            [
                'street' => 'okok',
                'city' => 'kota',
                'province' => 'provinsi',
                'country' => 'negara',
                'postal_code' => '1111',
            ],
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(200)
            ->assertJson([
                "data" => [
                    'street' => 'okok',
                    'city' => 'kota',
                    'province' => 'provinsi',
                    'country' => 'negara',
                    'postal_code' => '1111',
                ]
            ]);
    }

    public function testUpdateAddressFailed()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->get()->first();
        $this->put(
            '/api/contacts/' . $address->contact_id . '/addresses/' . $address->id,
            [
                'street' => 'okok',
                'city' => 'kota',
                'province' => 'provinsi',
                'country' => '',
                'postal_code' => '11111111111111',
            ],
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "country" => [
                        "The country field is required."
                    ],
                    "postal_code" => [
                        "The postal code field must not be greater than 10 characters."
                    ]
                ]
            ]);
    }

    public function testUpdateAddressNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->get()->first();
        $this->put(
            '/api/contacts/' . $address->contact_id . '/addresses/' . $address->id + 1,
            [
                'street' => 'okok',
                'city' => 'kota',
                'province' => 'provinsi',
                'country' => 'asdadd',
                'postal_code' => '111111',
            ],
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(404)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "not found"
                    ],
                ]
            ]);
    }

    public function testDeleteAddressSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->get()->first();
        $this->delete(
            '/api/contacts/' . $address->contact_id . '/addresses/' . $address->id,
            [
                
            ],
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(200)
            ->assertJson([
                "data" => true
            ]);
    }

    public function testDeleteAddressNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->get()->first();
        $this->delete(
            '/api/contacts/' . $address->contact_id . '/addresses/' . $address->id + 1,
            [
                
            ],
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(404)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "not found"
                    ]
                ]
            ]);
    }
}
