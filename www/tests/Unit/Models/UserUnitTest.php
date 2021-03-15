<?php

namespace Tests\Unit\Models;

use App\Models\User;
use PHPUnit\Framework\TestCase;

class UserUnitTest extends TestCase
{
    /** @var User $user */
    private $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = new User();
    }

    public function testFillableAttributes()
    {
        $fillable = ['name', 'email', 'password', 'typeable_id', 'typeable_type'];

        $this->assertEquals($this->user->getFillable(), $fillable);
    }

    public function testDatesAttributes()
    {
        $dates = ['created_at', 'updated_at'];

        foreach ($dates as $date) {
            $this->assertContains($date, $this->user->getDates());
        }

        $this->assertCount(count($dates), $this->user->getDates());
    }

    public function testIfUsingTraits()
    {
        $traits = [
            \App\Models\Traits\Uuid::class,
            \Laravel\Sanctum\HasApiTokens::class,
            \Illuminate\Notifications\Notifiable::class,
            \Illuminate\Database\Eloquent\SoftDeletes::class,
            \Illuminate\Database\Eloquent\Factories\HasFactory::class,
        ];

        $categoryTraits = array_keys(class_uses(User::class));

        $this->assertEqualsCanonicalizing($traits, $categoryTraits);
    }

    public function testCasts()
    {
        $casts = [
            'email_verified_at' => 'datetime',
            'deleted_at'        => 'datetime',
            'id'                => 'string',
            'typeable_id'       => 'string',
            'typeable_type'     => 'string'
        ];

        $this->assertEquals($casts, $this->user->getCasts());
    }

    public function testIncrementingAttribute()
    {
        $this->assertFalse($this->user->getIncrementing());
    }
}
