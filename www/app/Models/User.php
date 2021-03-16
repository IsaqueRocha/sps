<?php

namespace App\Models;

use App\Models\Traits\Uuid;
use App\Observers\UserObserver;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;
    use HasApiTokens;
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
    protected $fillable = [
        'name',
        'email',
        'password',
        'typeable_id',
        'typeable_type'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'deleted_at'        => 'datetime',
        'id'                => 'string',
        'typeable_id'       => 'string',
        'typeable_type'     => 'string'
    ];

    /**
     * Set the ID attributes to not be incremental.
     *
     * @var boolean
     */
    public $incrementing = false;

    /*
    |--------------------------------------------------------------------------
    | OVERRIDE METHODS
    |--------------------------------------------------------------------------
    */

    public function delete()
    {
        $typeable = $this->typeable_type;
        $type = $typeable::find($this->typeable_id);
        $type->delete();
        parent::delete();
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    protected $with = ['wallet'];

    /**
     * Get the child model (Customer or Seller).
     */
    public function typeable()
    {
        return $this->morphTo();
    }

    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    public function payments()
    {
        return $this->hasMany(Transaction::class, 'payer_id');
    }

    public function incomes()
    {
        return $this->hasMany(Transaction::class, 'payee_id');
    }

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        User::observe(UserObserver::class);
    }
}
