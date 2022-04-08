<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Laravel\Lumen\Auth\Authorizable;

class User extends Eloquent implements AuthorizableContract
{
    use Authenticatable, Authorizable, HasFactory;

    protected $collection = 'users';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        '_id', 'name', 'email', 'password', 'role_id', 'creator_id', 'address_id'
    ];



    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    public function addresses()
    {
        return $this->embedsMany(Address::class, '_id', 'user_id');
        // $instance = $this->newRelatedInstance(Address::class);

        // return $this->newHasMany(
        //     $instance->newQuery(),
        //     $this,
        //     'user_id',
        //     '_id'
        // );
    }
}
