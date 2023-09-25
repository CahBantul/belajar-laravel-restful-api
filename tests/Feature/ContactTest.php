<?php

namespace Tests\Feature;

use App\Models\Contact;
use Database\Seeders\ContactSeeder;
use Database\Seeders\SearchSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ContactTest extends TestCase
{
    public function testCreateSucces()
    {
        $this->seed([UserSeeder::class]);

        $this->post(
            '/api/contacts',
            [
                "first_name" => "nozami"
            ],
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(201)
            ->assertJson([
                "data" => [
                    "first_name" => "nozami",
                    "last_name" => null,
                    "email" => null,
                    "phone" => null
                ]
            ]);
    }

    public function testCreateFailed()
    {
        $this->seed([UserSeeder::class]);

        $this->post(
            '/api/contacts',
            [
                "first_name" => "nozami",
                "email" => 'Nozami'
            ],
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "email" => [
                        "The email field must be a valid email address."
                    ]
                ]
            ]);
    }

    public function testGetSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::query()->get()->first();

        $this->get(
            '/api/contacts/' . $contact->id,
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(200)
            ->assertJson([
                "data" => [
                    "first_name" => "Fardan",
                    "last_name" => "Nozami",
                    "email" => "nozami@gmail.com",
                    "phone" => "+6281229822979"
                ]
            ]);
    }

    public function testGetNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::query()->get()->first();

        $this->get(
            '/api/contacts/' . $contact->id + 1,
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

    public function testGetOtherUserContact()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::query()->get()->first();

        $this->get(
            '/api/contacts/' . $contact->id,
            [
                'Authorization' => 'test2'
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

    public function testUpdateSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::query()->get()->first();
        $this->put(
            '/api/contacts/' . $contact->id,
            [
                "first_name" => "Halo",
            ],
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(200)
            ->assertJson([
                "data" => [
                    "first_name" => "Halo",
                    "last_name" => "Nozami",
                    "email" => "nozami@gmail.com",
                    "phone" => "+6281229822979"
                ]
            ]);
    }

    public function testUpdateValidationError()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::query()->get()->first();
        $this->put(
            '/api/contacts/' . $contact->id,
            [
                "first_name" => "",
            ],
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "first_name" => [
                        "The first name field is required."
                    ]
                ]
            ]);
    }

    public function testDeleteSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::query()->get()->first();
        $this->delete(
            uri: '/api/contacts/' . $contact->id,
            headers: [
                'Authorization' => 'test'
            ]
        )->assertStatus(200)
            ->assertJson([
                "data" => true
            ]);
    }

    public function testDeleteNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::query()->get()->first();
        $this->delete(
            uri: '/api/contacts/' . $contact->id + 1,
            headers: [
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

    public function testSearchByName()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);
        $response = $this->get('/api/contacts?name=Fardan', [
                        'Authorization' => 'test'
                    ])
                    ->assertStatus(200)
                    ->json();
                    
        $this->assertCount(10, $response['data']);
    }

    public function testSearchByEmail()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);
        $response = $this->get('/api/contacts?email=nozami', [
                        'Authorization' => 'test'
                    ])
                    ->assertStatus(200)
                    ->json();

        $this->assertCount(10, $response['data']);
    }

    public function testSearchByPhone()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);
        $response = $this->get('/api/contacts?phone=1111', [
                        'Authorization' => 'test'
                    ])
                    ->assertStatus(200)
                    ->json();

        $this->assertCount(10, $response['data']);
    }
}
