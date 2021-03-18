<?php

namespace App\Services;

use Http;

class TransactionService
{
    public function getPermission()
    {
        $response = Http::retry(5, 100)->get(env('TRANSACTION_SERVICE_AUTHORIZER'));

        if ($response->json('message') === 'Autorizado') {
            return true;
        }

        return false;
    }
}
