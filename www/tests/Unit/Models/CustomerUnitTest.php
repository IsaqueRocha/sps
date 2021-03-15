<?php

namespace Tests\Unit\Models;

use App\Models\Customer;
use PHPUnit\Framework\TestCase;

class CustomerUnitTest extends TestCase
{
    /** @var Customer $customer */
    private $customer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customer = new Customer();
    }

    public function testFillableAttributes()
    {
        $fillable = ['cpf'];

        $this->assertEquals($this->customer->getFillable(), $fillable);
    }

    public function testDatesAttributes()
    {
        $dates = ['created_at', 'updated_at'];

        foreach ($dates as $date) {
            $this->assertContains($date, $this->customer->getDates());
        }

        $this->assertCount(count($dates), $this->customer->getDates());
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

        $this->assertEquals($casts, $this->customer->getCasts());
    }

    public function testIncrementingAttribute()
    {
        $this->assertFalse($this->user->getIncrementing());
    }
}
