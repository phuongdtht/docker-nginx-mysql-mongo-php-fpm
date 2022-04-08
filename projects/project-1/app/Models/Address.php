<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use App\Casts\ObjectIDCast;
use Laravel\Lumen\Auth\Authorizable;

class Address extends Eloquent
{
    use HasFactory;

    // /**
    //  * The attributes that should be cast.
    //  *
    //  * @var array
    //  */
    protected $casts = [
        'user_id' => ObjectIDCast::class,
    ];
    protected $primaryKey = '_id';

    protected $collection = 'addresses';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        '_id', 'user_id', 'address', 'city', 'phone', 'is_default', 'zip-code'
    ];

    public function user()
    {
        //return $this->embedsOne(User::class, 'user_id', '_id->oid');
        $instance = $this->newRelatedInstance(User::class);

        return $this->newHasOne(
            $instance->newQuery(),
            $this,
            '_id',
            'user_id->toString()'
        );
    }
}
