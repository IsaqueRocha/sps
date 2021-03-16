<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Seller;
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

        User::create([
            'name' => 'Isaque Rocha',
            'email' => 'isaquerocha@gmail.com',
            'password' => bcrypt('password'),
            'typeable_id' => $customer->id,
            'typeable_type' => get_class($customer)
        ]);

        $seller = Seller::create(['cnpj' => '91.901.769/0001-36']);

        User::create([
            'name' => 'Jon Doe',
            'email' => 'jon@doe.com',
            'password' => bcrypt('password'),
            'typeable_id' => $seller->id,
            'typeable_type' => get_class($seller)
        ]);
    }
}
