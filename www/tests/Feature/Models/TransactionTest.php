<?php

namespace Tests\Feature\Models;

use App\Models\Customer;
use App\Models\Seller;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    use DatabaseMigrations;

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

        $ss = Seller::create(['cnpj' => '91.901.769/0001-36']);

        $this->seller = User::create([
            'name' => 'Jon Doe',
            'email' => 'jon@doe.com',
            'password' => bcrypt('password'),
            'typeable_id' => $ss->id,
            'typeable_type' => get_class($ss)
        ]);
    }

    public function testCreate()
    {
        Transaction::create([
            'payer' => $this->customer->id,
            'payee' => $this->seller->id,
            'value' => 100.00
        ]);

        $transaction = Transaction::first();

        $this->assertEquals(100.00, $transaction->value);
        $this->assertEquals($this->customer->id, $transaction->payer);
        $this->assertEquals($this->seller->id, $transaction->payee);
    }
}
