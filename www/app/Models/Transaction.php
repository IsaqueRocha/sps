<?php

namespace App\Models;

use App\Models\Traits\Uuid;
use App\Observers\TransactionObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Uuid;

    /*
    |--------------------------------------------------------------------------
    | ELOQUENT ATTRIBUTES
    |--------------------------------------------------------------------------
    */

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['value', 'payer', 'payee'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'         => 'string',
        'payer'      => 'string',
        'payee'      => 'string',
        'deleted_at' => 'datetime'
    ];

    /**
     * Set the ID attributes to not be incremental.
     *
     * @var boolean
     */
    public $incrementing = false;

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    public function userPayer()
    {
        return $this->belongsTo(User::class, 'payer', 'id');
    }

    public function userPayee()
    {
        return $this->belongsTo(User::class, 'payee', 'id');
    }

    /*
    |--------------------------------------------------------------------------
    |
    |--------------------------------------------------------------------------
    */

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        Transaction::observe(TransactionObserver::class);
    }
}
