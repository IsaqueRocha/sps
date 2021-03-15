<?php

namespace Tests\Unit\Models;

use App\Models\Wallet;
use PHPUnit\Framework\TestCase;

class WalletUnitTest extends TestCase
{
    /** @var Wallet $wallet */
    private $wallet;

    protected function setUp(): void
    {
        parent::setUp();
        $this->wallet = new Wallet();
    }

    public function testFillableAttributes()
    {
        $fillable = [
            'funds',
            'user_id'
        ];
        $this->assertEquals($this->wallet->getFillable(), $fillable);
    }

    public function testDatesAttributes()
    {
        $dates = ['created_at', 'updated_at'];

        foreach ($dates as $date) {
            $this->assertContains($date, $this->wallet->getDates());
        }

        $this->assertCount(count($dates), $this->wallet->getDates());
    }

    public function testIfUsingTraits()
    {
        $traits = [
            \App\Models\Traits\Uuid::class,
            \Illuminate\Database\Eloquent\SoftDeletes::class,
            \Illuminate\Database\Eloquent\Factories\HasFactory::class,
        ];

        $walletTraits = array_keys(class_uses(Wallet::class));

        $this->assertEqualsCanonicalizing($traits, $walletTraits);
    }

    public function testCasts()
    {
        $casts = [
            'id'         => 'string',
            'funds'      => 'double',
            'deleted_at' => 'datetime'
        ];

        $this->assertEquals($casts, $this->wallet->getCasts());
    }

    public function testIncrementingAttribute()
    {
        $this->assertFalse($this->wallet->getIncrementing());
    }
}
