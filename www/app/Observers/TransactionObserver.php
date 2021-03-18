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
        $uri = env('TRANSACTION_SERVIME_MESSENGER');
        Http::retry(3, 100)->get($uri, ['user' => $transaction->payee]);
    }
}
