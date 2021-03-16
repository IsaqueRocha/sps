<?php

namespace Tests\Feature\Services;

use Tests\TestCase;
use App\Services\TransactionService;

class TransactionServiceTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testGetPermission()
    {
        $service = new TransactionService();

        $service->getPermission();

        $this->assertTrue($service->getPermission());
    }
}
