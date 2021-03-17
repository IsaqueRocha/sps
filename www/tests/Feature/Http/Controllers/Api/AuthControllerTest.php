<?php

namespace Tests\Feature\Http\Controllers\Api;

use Tests\TestCase;
use App\Models\User;
use App\Models\Seller;
use App\Models\Customer;
use Illuminate\Http\Response;
use Tests\Traits\TestInvalidation;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class AuthControllerTest extends TestCase
{
    use DatabaseMigrations;
    use TestInvalidation;

    private $data;

    /*
    |--------------------------------------------------------------------------
    | URL CONSTANTS
    |--------------------------------------------------------------------------
    */

    private const STORE  = '/api/register';

    /*
    |--------------------------------------------------------------------------
    | TEST CONFIGURATION
    |--------------------------------------------------------------------------
    */

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

    /*
    |--------------------------------------------------------------------------
    | TEST FUNCTIONS
    |--------------------------------------------------------------------------
    */

    // ! POSITIVE TESTS

    public function testCustomerResgistration()
    {
        $sendData = $this->data + [
            'type' => 'customer',
            'cpf'  => '177.132.774-04'
        ];

        $response = $this->json('POST', self::STORE, $sendData);
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

    public function testCustomerCannotLogin()
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
            'password' => 'wrongpassword'
        ];

        $response = $this->json('POST', '/api/login', $sendData);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testSellerResgistration()
    {
        $sendData = $this->data + [
            'type' => 'seller',
            'cnpj' => '91.901.769/0001-36'
        ];

        $response = $this->json('POST', self::STORE, $sendData);
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

    public function testSellerCannotLogin()
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
            'password' => 'wrongpassword'
        ];

        $response = $this->json('POST', '/api/login', $sendData);
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    // !NEGATIVE TESTS

    public function testInvalidaData()
    {
        /** @var User $user */
        $user = User::factory()->create();

        // TEST NAME
        $sendData = ['name' => '',];
        $this->assertRegisterInvalidation($sendData, 'required', 'name');

        $sendData = ['name' => 'a',];
        $this->assertRegisterInvalidation($sendData, 'min.string', 'name', ['min' => 3]);

        $sendData = ['name' => str_repeat('a', 256),];
        $this->assertRegisterInvalidation($sendData, 'max.string', 'name', ['max' => 255]);

        // TEST E-MAIL
        $sendData = ['email' => '',];
        $this->assertRegisterInvalidation($sendData, 'required', 'email');

        $sendData = ['email' => 'isaque',];
        $this->assertRegisterInvalidation($sendData, 'email', 'email');

        $sendData = ['email' => $user->email];
        $this->assertRegisterInvalidation($sendData, 'unique', 'email');

        // TEST PASSWORD
        $sendData = ['password' => ''];
        $this->assertRegisterInvalidation($sendData, 'required', 'password');

        $sendData = ['password' => '123'];
        $this->assertRegisterInvalidation($sendData, 'min.string', 'password', ['min' => 6]);

        $sendData = ['confirmation_password' => ''];
        $this->assertRegisterInvalidation($sendData, 'required', 'confirmation_password');

        $sendData = ['password' => '123', 'confirmation_password' => 'qwer'];
        $this->assertRegisterInvalidation($sendData, 'same', 'confirmation_password', ['other' => 'password']);

        // TEST TYPE
        $sendData = ['type' => '',];
        $this->assertRegisterInvalidation($sendData, 'required', 'type');

        $sendData = ['type' => 'user',];
        $this->assertRegisterInvalidation($sendData, 'in', 'type');

        $this->assertInvalidDoc('customer', 'cpf', '110.100.010-11', '17713277404');
        $this->assertInvalidDoc('seller', 'cnpj', '11.111.111/0001-11', '91901769/000136');
    }


    /*
    |--------------------------------------------------------------------------
    | HELPER FUNCTIONS
    |--------------------------------------------------------------------------
    */

    private function assertInvalidDoc($userType, $docType, $docInvalid, $docUnformated)
    {
        $sendData = ['type' => $userType, $docType => ''];
        $this->assertRegisterInvalidation($sendData, 'required', $docType);

        $sendData = ['type' => $userType, $docType => $docInvalid];
        $response = $this->json('POST', self::STORE, $sendData);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonFragment([$docType => [strtoupper($docType) . ' inválido']]);

        $sendData = ['type' => $userType, $docType => $docUnformated];
        $response = $this->json('POST', self::STORE, $sendData);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonFragment([$docType => ['Formato inválido para ' . strtoupper($docType)]]);
    }

    protected function routeStore()
    {
        return self::STORE;
    }
}
