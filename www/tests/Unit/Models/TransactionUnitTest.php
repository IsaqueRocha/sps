<?php

namespace Tests\Unit\Models;

use App\Models\Transaction;
use PHPUnit\Framework\TestCase;

class TransactionUnitTest extends TestCase
{
    /** @var Transaction $transaction */
    private $transaction;

    protected function setUp(): void
    {
        parent::setUp();
        $this->transaction = new Transaction();
    }

    public function testFillableAttributes()
    {
        $fillable = ['value', 'payer', 'payee'];
        $this->assertEquals($fillable, $this->transaction->getFillable());
    }

    public function testDatesAttributes()
    {
        $dates = ['created_at', 'updated_at'];

        foreach ($dates as $date) {
            $this->assertContains($date, $this->transaction->getDates());
        }

        $this->assertCount(count($dates), $this->transaction->getDates());
    }

    public function testIfUsingTraits()
    {
        $traits = [
            \App\Models\Traits\Uuid::class,
            \Illuminate\Database\Eloquent\SoftDeletes::class,
            \Illuminate\Database\Eloquent\Factories\HasFactory::class,
        ];

        $transactionTraits = array_keys(class_uses(Transaction::class));

        $this->assertEqualsCanonicalizing($traits, $transactionTraits);
    }

    public function testCasts()
    {
        $casts = [
            'id'         => 'string',
            'payer'      => 'string',
            'payee'      => 'string',
            'deleted_at' => 'datetime'
        ];

        $this->assertEquals($casts, $this->transaction->getCasts());
    }

    public function testIncrementingAttribute()
    {
        $this->assertFalse($this->transaction->getIncrementing());
    }
}
