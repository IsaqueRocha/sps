<?php

namespace Tests\Feature\Http\Controllers\Api;

use Tests\TestCase;
use App\Models\User;
use App\Models\Seller;
use App\Models\Customer;
use Laravel\Sanctum\Sanctum;
use Illuminate\Http\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class TransactionControllerTest extends TestCase
{
    use DatabaseMigrations;
    use RefreshDatabase;

    /** @var User $customer */
    private $customer;
    /** @var User $seller */
    private $seller;

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
    }

    public function testCustomerCanCreate()
    {
        Sanctum::actingAs($this->customer);

        $sendData = [
            'payer' => $this->customer->id,
            'payee' => $this->seller->id,
            'value' => 100.00
        ];

        $response = $this->json('POST', 'api/transaction', $sendData);
        $response->assertStatus(Response::HTTP_CREATED);

        $this->customer->wallet->refresh();
        $this->seller->wallet->refresh();

        $this->assertEquals(900.00, $this->customer->wallet->funds);
        $this->assertEquals(600.00, $this->seller->wallet->funds);
    }

    public function testSellerCannotCreate()
    {
        Sanctum::actingAs($this->seller);

        $sendData = [
            'payer' => $this->seller->id,
            'payee' => $this->customer->id,
            'value' => 100.00
        ];

        $response = $this->json('POST', 'api/transaction', $sendData);
        $response->assertStatus(Response::HTTP_FORBIDDEN);

        $this->customer->wallet->refresh();
        $this->seller->wallet->refresh();

        $this->assertEquals(500.00, $this->seller->wallet->funds);
        $this->assertEquals(1000.00, $this->customer->wallet->funds);
    }
}
