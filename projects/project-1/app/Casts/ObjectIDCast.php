<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use MongoDB\BSON\ObjectID;

class ObjectIDCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  \Jenssegers\Mongodb\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return array
     */
    public function get($model, $key, $value, $attributes)
    {
        return (string) $value;
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  \Jenssegers\Mongodb\Eloquent\Model  $model
     * @param  string  $key
     * @param  array  $value
     * @param  array  $attributes
     * @return string
     */
    public function set($model, $key, $value, $attributes)
    {
        return new ObjectID($value);
    }
}
