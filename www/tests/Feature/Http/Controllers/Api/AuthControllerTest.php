<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\Response;

class AuthControllerTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * Test the creation of a new user.
     *
     * @return void
     */
    public function testResgister()
    {
        $sendData = [
            'name'                  => 'Jon',
            'email'                 => 'jon@doe.com',
            'password'              => 'password',
            'confirmation_password' => 'password'
        ];

        $response = $this->json('POST', '/api/register', $sendData);

        $response->assertStatus(Response::HTTP_CREATED);
    }

    public function testLogin()
    {
        $user = User::factory()->create();

        $sendData = [
            'email' => $user->email,
            'password' => 'password'
        ];

        $response = $this->json('POST', '/api/login', $sendData);

        $response->assertStatus(Response::HTTP_OK);

    }
}
