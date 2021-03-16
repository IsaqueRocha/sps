<?php

namespace App\Observers;

use App\Models\Transaction;
use Http;

class TransactionObserver
{
    /**
     * Handle the Transaction "created" event.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return void
     */
    public function created(Transaction $transaction)
    {
        // TODO: Simular mensageria(?)
        $uri = 'https://run.mocky.io/v3/b19f7b9f-9cbf-4fc6-ad22-dc30601aec04';
        Http::retry(3, 100)->get($uri, ['user' => $transaction->payee]);
    }
}
