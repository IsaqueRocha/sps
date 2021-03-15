<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $customer = Customer::create([
            'cpf' => '177.132.774-04'
        ]);

        User::factory()->create([
            'name' => 'Isaque Rocha',
            'email' => 'isaquerocha@gmail.com',
            'password' => bcrypt('password'),
            'typeable_id' => $customer->id,
            'typeable_type' => get_class($customer)
        ]);

        User::factory(9)->create();
    }
}
