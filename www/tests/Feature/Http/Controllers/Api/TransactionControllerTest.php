<?php

namespace Tests\Feature\Http\Controllers\Api;

use Mockery;
use Tests\TestCase;
use App\Models\User;
use App\Models\Seller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Laravel\Sanctum\Sanctum;
use Illuminate\Http\Response;
use Tests\Traits\TestInvalidation;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Controllers\Api\TransactionController;
use App\Models\Wallet;
use Exception;
use Illuminate\Foundation\Testing\DatabaseMigrations;

use function PHPUnit\Framework\throwException;

class TransactionControllerTest extends TestCase
{
    use DatabaseMigrations;
    use RefreshDatabase;
    use TestInvalidation;
    use WithFaker;

    /*
    |--------------------------------------------------------------------------
    | URL CONSTANTS
    |--------------------------------------------------------------------------
    */

    private const STORE  = 'api/transaction';

/*
    |--------------------------------------------------------------------------
    | CLASS VARIABLES
    |--------------------------------------------------------------------------
    */

    /** @var User $customer */
    private $customer;
    /** @var User $seller */
    private $seller;

    private $sendData;

    /*
    |--------------------------------------------------------------------------
    | TEST CONFIGURATION
    |--------------------------------------------------------------------------
    */

    protected function setUp(): void
    {
        parent::setUp();

        $cc = Customer::create([
            'cpf' => '177.132.774-04'
        ]);

        $this->customer = User::create([
            'name' => 'Isaque Rocha',
            'email' => 'isaquerocha@gmail.com',
            'password' => bcrypt('password'),
            'typeable_id' => $cc->id,
            'typeable_type' => get_class($cc)
        ]);

        $this->customer->refresh();
        $this->customer->wallet->funds = 1000.00;
        $this->customer->wallet->save();

        $ss = Seller::create(['cnpj' => '91.901.769/0001-36']);

        $this->seller = User::create([
            'name' => 'Jon Doe',
            'email' => 'jon@doe.com',
            'password' => bcrypt('password'),
            'typeable_id' => $ss->id,
            'typeable_type' => get_class($ss)
        ]);

        $this->seller->refresh();
        $this->seller->wallet->funds = 500.00;
        $this->seller->wallet->save();

        $this->sendData = [
            'payer' => $this->customer->id,
            'payee' => $this->seller->id,
            'value' => 100.00
        ];
    }

     /*
    |--------------------------------------------------------------------------
    | TEST FUNCTIONS
    |--------------------------------------------------------------------------
    */

    // ! POSITIVE TESTS

    public function testCustomerCanTransfer()
    {
        Sanctum::actingAs($this->customer);

        $response = $this->json('POST', self::STORE, $this->sendData);
        $response->assertStatus(Response::HTTP_CREATED);

        $this->customer->wallet->refresh();
        $this->seller->wallet->refresh();

        $this->assertEquals(900.00, $this->customer->wallet->funds);
        $this->assertEquals(600.00, $this->seller->wallet->funds);
    }

    public function testSellerCannotTransfer()
    {
        Sanctum::actingAs($this->seller);

        $response = $this->json('POST', self::STORE, $this->sendData);
        $response->assertStatus(Response::HTTP_FORBIDDEN);

        $this->customer->wallet->refresh();
        $this->seller->wallet->refresh();

        $this->assertEquals(500.00, $this->seller->wallet->funds);
        $this->assertEquals(1000.00, $this->customer->wallet->funds);
    }

    public function testRollBack()
    {
        Sanctum::actingAs($this->customer);

        $controller = Mockery::mock(TransactionController::class)
                            ->makePartial()
                            ->shouldAllowMockingProtectedMethods();
        $controller->shouldReceive('validate')->andReturn($this->sendData);
        $controller->shouldReceive('getPermission')->andReturn(false);

        $request = Mockery::mock(Request::class);

        /** @var JsonResponse $response */
        $response = $controller->store($request);
        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->status());
        $this->assertEquals('{"error":"Error Processing Permission Request"}', $response->content());
    }

    // ! NEGATIVE TESTS

    public function testInvalidData()
    {
        Sanctum::actingAs($this->customer);

        $data = array_merge($this->sendData, ['payer' => '']);
        $this->assertRegisterInvalidation($data, 'required', 'payer');

        $data = array_merge($this->sendData, ['payer' => '7662f117-a1c1-46d1-917e']);
        $this->assertRegisterInvalidation($data, 'uuid', 'payer');

        $data = array_merge($this->sendData, ['payee' => '']);
        $this->assertRegisterInvalidation($data, 'required', 'payee');

        $data = array_merge($this->sendData, ['payee' => '7662f117-a1c1-46d1-917e']);
        $this->assertRegisterInvalidation($data, 'uuid', 'payee');

        $data = array_merge($this->sendData, ['value' => '']);
        $this->assertRegisterInvalidation($data, 'required', 'value');

        $data = array_merge($this->sendData, ['value' => -10.0]);
        $response = $this->json('POST', self::STORE, $data);
        $response->assertStatus(Response::HTTP_BAD_REQUEST);

        $data = array_merge($this->sendData, ['value' => 0.0]);
        $response = $this->json('POST', self::STORE, $data);
        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER FUNCTIONS
    |--------------------------------------------------------------------------
    */

    protected function routeStore()
    {
        return self::STORE;
    }
}
