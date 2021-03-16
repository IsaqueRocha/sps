<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\Wallet;
use App\Services\TransactionService;

class TransactionRepository
{
    private $service;

    public function __construct(TransactionService $service)
    {
        $this->service = $service;
    }

    /**
     * Get the payer wallet
     */
    public function getUserWallet(string $id)
    {
        /** @var User $user */
        $user = User::find($id);
        $user->refresh();

        return $user->wallet;
    }

    public function addFunds(Wallet $wallet, float $value)
    {
        $wallet->funds += $value;
    }

    public function removeFunds(Wallet $wallet, float $value)
    {
        $wallet->funds -= $value;
    }

    public function getPermission()
    {
        return $this->service->getPermission();
    }
}
