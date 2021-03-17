<?php

namespace Tests\Feature\Models;

use Tests\TestCase;
use App\Models\User;
use Ramsey\Uuid\Uuid;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class WalletTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;
    use DatabaseMigrations;

    private $testKeys;

    private $user;
    private $wallet;

    protected function setUp(): void
    {
        parent::setUp();

        $this->faker = $this->makeFaker('pt_BR');

        $this->testKeys = [
            'id',
            'funds',
            'user_id',
            'created_at',
            'updated_at',
            'deleted_at',
        ];

        $this->user = User::factory()->create();
        $this->user->refresh();

        $this->wallet = $this->user->wallet;
    }

    public function testList()
    {
        User::factory(10)->create();
        $wallets = Wallet::all();
        $walletKeys = array_keys($wallets->first()->getAttributes());

        $this->assertCount(11, $wallets);
        $this->assertEqualsCanonicalizing($this->testKeys, $walletKeys);
    }

    public function testCreate()
    {
        $this->assertTrue(Uuid::isValid($this->wallet->id));
        $this->assertEquals(0.0, $this->wallet->funds);
    }

    public function testUpdate()
    {
        $this->assertEquals(0.0, $this->wallet->funds);

        $this->wallet->funds = 1000.0;
        $this->wallet->save();

        $newWallet = Wallet::find($this->wallet->id);

        $this->assertEquals(1000.0, $newWallet->funds);
    }

    public function testDelete()
    {
        $this->user->delete();
        $this->assertDatabaseMissing('wallets', $this->wallet->toArray());
    }
}
