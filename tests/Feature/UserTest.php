<?php

namespace Tests\Feature;

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
}
