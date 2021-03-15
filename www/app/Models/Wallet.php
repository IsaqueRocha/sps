<?php

namespace App\Models;

use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Wallet extends Model
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
    protected $fillable = [
        'funds',
        'user_id'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'         => 'string',
        'funds'      => 'double',
        'deleted_at' => 'datetime'
    ];

    /**
     * Set the ID attributes to not be incremental.
     *
     * @var boolean
     */
    public $incrementing = false;


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
