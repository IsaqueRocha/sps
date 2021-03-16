<?php

namespace Database\Seeders;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;

class TransactionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::all();

        $customer = $users->first();
        $seller = $users->last();

        Transaction::create([
            'payer' => $customer->id,
            'payee' => $seller->id,
            'value' => 100.00
        ]);
    }
}
