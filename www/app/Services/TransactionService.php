<?php

namespace App\Services;

use Http;

class TransactionService
{
    public function getPermission()
    {
        $response = Http::retry(5, 100)->get('https://run.mocky.io/v3/8fafdd68-a090-496f-8c9a-3442cf30dae6');

        if ($response->json('message') === 'Autorizado') {
            return true;
        }

        return false;
    }
}
