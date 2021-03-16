<?php

namespace Tests\Feature\Models;

use Tests\TestCase;
use App\Models\User;
use Ramsey\Uuid\Uuid;
use App\Models\Seller;
use App\Models\Customer;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class UserTest extends TestCase
{
    use DatabaseMigrations;
    use RefreshDatabase;
    use WithFaker;

    private $testKeys;

    protected function setUp(): void
    {
        parent::setUp();

        User::boot();

        $this->faker = $this->makeFaker('pt_BR');

        $this->testKeys = [
            'id',
            'name',
            'email',
            'email_verified_at',
            'password',
            'typeable_id',
            'typeable_type',
            'created_at',
            'updated_at',
            'deleted_at',
            'remember_token'
        ];
    }

    public function testList()
    {
        User::factory(10)->create();
        $users = User::all();
        $this->assertCount(10, $users);

        $this->assertUserFields($users->first());
    }

    public function testCreate()
    {
        $user = User::factory()->create(['name' => 'New User']);
        $user->refresh();

        $this->assertTrue(Uuid::isValid($user->id));
        $this->assertEquals('New User', $user->name);
        $this->assertNotNull($user->typeable_id);
        $this->assertNotNull($user->typeable_type);
        $this->assertUserFields($user);

        $customer = Customer::create(['cpf' => $this->faker->cpf]);

        $user = User::create([
            'name' => $name = $this->faker->name,
            'email' => $email = $this->faker->safeEmail,
            'password' => bcrypt('password'),
            'typeable_id' => $customer->id,
            'typeable_type' => get_class($customer)
        ]);

        $user->refresh();

        $this->assertTrue(Uuid::isValid($user->id));
        $this->assertEquals($customer->id, $user->typeable_id);
        $this->assertEquals(Customer::class, $user->typeable_type);
        $this->assertEquals($name, $user->name);
        $this->assertEquals($email, $user->email);
        $this->assertEquals(0.0, $user->wallet->funds);
    }

    public function testUpdate()
    {
        $customer = Customer::create(['cpf' => $this->faker->cpf]);

        /** @var User $user */
        $user = User::factory()->create([
            'typeable_id' => $customer->id,
            'typeable_type' => get_class($customer)
        ]);

        $data = [
            'name' => 'Isaque Rocha',
            'email' => 'isaque@gmail.com'
        ];

        $user->update($data);

        foreach ($data as $key => $value) {
            $this->assertEquals($value, $user->{$key});
        }

        $seller = Seller::create(['cnpj' => $this->faker->cnpj]);

        $data = [
            'typeable_id' => $seller->id,
            'typeable_type' => get_class($seller)
        ];

        $user->update($data);
        foreach ($data as $key => $value) {
            $this->assertEquals($value, $user->{$key});
        }
    }

    public function testDelete()
    {
        /** @var User $user */
        $user = User::factory()->create();
        $type = $user->typeable_type;

        $user->delete();

        $this->assertNull(User::find($user->id));
        $this->assertNull($type::find($user->typeable_id));
    }

    private function assertUserFields(User $user)
    {
        $userKeys = array_keys($user->getAttributes());
        if (strcmp($user->typeable_type, Customer::class) == 0) {
            $this->testKeys[] = 'cpf';
        } else {
            $this->testKeys[] = 'cnpj';
        }

        $this->assertEqualsCanonicalizing($this->testKeys, $userKeys);
    }
}
