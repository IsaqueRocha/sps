<?php

namespace Tests\Feature\Models;

use App\Models\User;
use App\Models\Wallet;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Ramsey\Uuid\Uuid;

class WalletTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;
    use DatabaseMigrations;

    private $testKeys;

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
    }

    public function testList()
    {
        User::factory(10)->create();
        $wallets = Wallet::all();
        $walletKeys = array_keys($wallets->first()->getAttributes());

        $this->assertCount(10, $wallets);
        $this->assertEqualsCanonicalizing($this->testKeys, $walletKeys);
    }

    public function testCreate()
    {
        $user = User::factory()->create();
        $user->refresh();

        $wallet = $user->wallet;

        $this->assertTrue(Uuid::isValid($wallet->id));
    }
}
