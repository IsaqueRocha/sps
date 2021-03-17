<?php

namespace Tests\Feature\Http\Controllers\Api;

use Tests\TestCase;
use App\Models\User;
use App\Models\Customer;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ProfileController extends TestCase
{
    use DatabaseMigrations;

    private $user;

    protected function setUp(): void
    {
        parent::setUp();
        $customer = Customer::create([
            'cpf' => '177.132.774-04'
        ]);
        $this->user = User::factory()->create([
            'typeable_id' => $customer->id,
            'typeable_type' => get_class($customer)
        ]);
    }

    public function testIndex()
    {
        Sanctum::actingAs($this->user);

        $response = $this->json('GET', 'api/profile');

        $response->assertStatus(200);
    }
}
