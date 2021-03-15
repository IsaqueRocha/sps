<?php

namespace Tests\Feature\Http\Controllers\Api;

use Tests\TestCase;
use App\Models\User;
use App\Models\Seller;
use App\Models\Customer;
use Illuminate\Http\Response;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class AuthControllerTest extends TestCase
{
    use DatabaseMigrations;

    private $data;

    protected function setUp(): void
    {
        parent::setUp();

        $this->data = [
            'name'                  => 'Jon',
            'email'                 => 'jon@doe.com',
            'password'              => 'password',
            'confirmation_password' => 'password',
        ];
    }

    public function testCustomerResgistration()
    {
        $sendData = $this->data + [
            'type' => 'customer',
            'cpf'  => '177.132.774-04'
        ];


        $response = $this->json('POST', '/api/register', $sendData);
        $response->assertStatus(Response::HTTP_CREATED);

        $id = $response->json('data.user.id');
        $user = User::findOrFail($id);
        $this->assertEquals($user->cpf, $sendData['cpf']);
    }

    public function testCustomerLogin()
    {
        $customer = Customer::create([
            'cpf' => '177.132.774-04'
        ]);
        $user = User::factory()->create([
            'typeable_id' => $customer->id,
            'typeable_type' => get_class($customer)
        ]);

        $sendData = [
            'email'    => $user->email,
            'password' => 'password'
        ];

        $response = $this->json('POST', '/api/login', $sendData);

        $response->assertStatus(Response::HTTP_OK);
    }

    public function testSellerResgistration()
    {
        $sendData = $this->data + [
            'type' => 'seller',
            'cnpj' => '91.901.769/0001-36'
        ];

        $response = $this->json('POST', '/api/register', $sendData);
        $response->assertStatus(Response::HTTP_CREATED);

        $id = $response->json('data.user.id');
        $user = User::findOrFail($id);
        $this->assertEquals($user->cnpj, $sendData['cnpj']);
    }

    public function testSellerLogin()
    {
        $seller = Seller::create([
            'cnpj' => '91.901.769/0001-36'
        ]);
        $user = User::factory()->create([
            'typeable_id' => $seller->id,
            'typeable_type' => get_class($seller)
        ]);

        $sendData = [
            'email' => $user->email,
            'password' => 'password'
        ];

        $response = $this->json('POST', '/api/login', $sendData);
        $response->assertStatus(Response::HTTP_OK);
    }
}
