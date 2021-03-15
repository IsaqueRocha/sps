<?php

namespace Tests\Unit\Models;

use App\Models\Seller;
use PHPUnit\Framework\TestCase;

class SellerUnitTest extends TestCase
{
    /** @var Seller $seller */
    private $seller;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seller = new Seller();
    }

    public function testFillableAttributes()
    {
        $fillable = ['cnpj'];

        $this->assertEquals($this->seller->getFillable(), $fillable);
    }

    public function testDatesAttributes()
    {
        $dates = ['created_at', 'updated_at'];

        foreach ($dates as $date) {
            $this->assertContains($date, $this->seller->getDates());
        }

        $this->assertCount(count($dates), $this->seller->getDates());
    }

    public function testIfUsingTraits()
    {
        $traits = [
            \App\Models\Traits\Uuid::class,
            \Illuminate\Database\Eloquent\SoftDeletes::class,
            \Illuminate\Database\Eloquent\Factories\HasFactory::class,
        ];

        $categoryTraits = array_keys(class_uses(Customer::class));

        $this->assertEqualsCanonicalizing($traits, $categoryTraits);
    }

    public function testCasts()
    {
        $casts = [
            'deleted_at' => 'datetime',
            'id'         => 'string',
        ];

        $this->assertEquals($casts, $this->seller->getCasts());
    }

    public function testIncrementingAttribute()
    {
        $this->assertFalse($this->user->getIncrementing());
    }
}
