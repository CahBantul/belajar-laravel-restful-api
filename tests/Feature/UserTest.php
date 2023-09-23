<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function testRegisterSuccess()
    {
        $this->post("/api/users", [
            'username' => 'nozami',
            'password' => 'password',
            'name' => 'nozami',
        ])
            ->assertStatus(201)
            ->assertJson([
                "data" => [
                    'username' => 'nozami',
                    'name' => 'nozami',
                ]
            ]);
    }

    public function testRegisterFailed()
    {
        $this->post("/api/users", [
            'username' => '',
            'password' => '',
            'name' => '',
        ])
            ->assertStatus(400)
            ->assertJson([
                "errors" => [
                    'username' => [
                        "The username field is required."
                    ],
                    'name' => [
                        "The name field is required."
                    ],
                    "password" => [
                        "The password field is required."
                    ]
                ]
            ]);
    }

    public function testRegisterUsernameAlreadyRegistered()
    {
        $this->testRegisterSuccess();

        $this->post("/api/users", [
            'username' => 'nozami',
            'password' => 'password',
            'name' => 'nozami',
        ])
            ->assertStatus(400)
            ->assertJson([
                "errors" => [
                    'username' => [
                        'username already registered',
                    ]
                ]
            ]);
    }

    public function testLoginSuccess()
    {
        $this->seed([UserSeeder::class]);
        $this->post("/api/users/login", [
                'username' => 'test',
                'password' => 'test',
            ])
            ->assertStatus(200);
        $user = User::where('username', 'test')->first();
        $this->assertNotNull($user->token);
    }

    public function testLoginFailedUserNotFound()
    {
        $this->post("/api/users/login", [
                'username' => 'test',
                'password' => 'test',
            ])
            ->assertStatus(401)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "username or password wrong"
                    ]
                ]
            ]);
    }

    public function testLoginFailedPasswordWrong()
    {
        $this->seed([UserSeeder::class]);
        $this->post("/api/users/login", [
                'username' => 'test',
                'password' => 'salah',
            ])
            ->assertStatus(401)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "username or password wrong"
                    ]
                ]
            ]);
    }

    public function testGetSuccess()
    {
        $this->seed([UserSeeder::class]);
        $this->get("/api/users/current", [
                'Authorization' => 'test'
            ])
            ->assertStatus(200)
            ->assertJson([
                "data" => [
                    "name" => "test",
                    "username" => "test",
                ]
            ]);  
    }

    public function testGetInvalidToken()
    {
        $this->seed([UserSeeder::class]);
        $this->get("/api/users/current", [
                'Authorization' => 'salah'
            ])
            ->assertStatus(401)
            ->assertJson([
                "errors" =>[
                    "message" => [
                        "Unauthorized"
                     ]
                ]
            ]);  
    }

    public function testGetUnauthorized()
    {
        $this->seed([UserSeeder::class]);
        $this->get("/api/users/current")
            ->assertStatus(401)
            ->assertJson([
                "errors" =>[
                    "message" => [
                        "Unauthorized"
                     ]
                ]
            ]);  
    }

    public function testUpdatePasswordSuccess()
    {
        $this->seed([UserSeeder::class]);
        $oldUser = User::where('username', 'test')->first();
        $this->patch("/api/users/current", [
                'password' => 'baru'
            ],
   [
                'Authorization' => 'test'
            ])
            ->assertStatus(200)
            ->assertJson([
                "data" => [
                    "name" => "test",
                    "username" => "test",
                ]
            ]);  
        $newUser = User::where('username', 'test')->first();
        $this->assertNotEquals($oldUser->password, $newUser->password);
    }

    public function testUpdateNameSuccess()
    {
        $this->seed([UserSeeder::class]);
        $oldUser = User::where('username', 'test')->first();
        $this->patch("/api/users/current", [
                'name' => 'Nozami'
            ],
   [
                'Authorization' => 'test'
            ])
            ->assertStatus(200)
            ->assertJson([
                "data" => [
                    "name" => "Nozami",
                    "username" => "test",
                ]
            ]);  
        $newUser = User::where('username', 'test')->first();
        $this->assertNotEquals($oldUser->name, $newUser->name);
    }

    public function testUpdateFailed()
    {
        $this->seed([UserSeeder::class]);
        $this->patch("/api/users/current", [
                'name' => "Lorem ipsum dolor sit amet consectetur, adipisicing elit. Deleniti nulla modi quos officiis voluptate deserunt numquam, odit aliquid id voluptas?"
            ],
   [
                'Authorization' => 'test'
            ])
            ->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "name" => [
                        "The name field must not be greater than 100 characters."
                    ]
                ]
            ]);  
    }

    public function testLogout()
    {
        $this->seed([UserSeeder::class]);

        $this->delete(uri: '/api/users/logout', headers: ['Authorization' => 'test'])
            ->assertStatus(200)
            ->assertJson([
                'data' => true
            ]);
        
        $user = User::where('username', 'test')->first();
        $this->assertNull($user->token);
    }

    public function testLogoutFailed()
    {
        $this->seed([UserSeeder::class]);

        $this->delete(uri: '/api/users/logout')
            ->assertStatus(401)
            ->assertJson([
                'errors' => [
                    "message" => [
                        "Unauthorized"
                    ]
                ]
            ]);
    }
}
