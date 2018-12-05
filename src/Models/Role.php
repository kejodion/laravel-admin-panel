<?php

namespace Kjjdion\LaravelAdminPanel\Models;

use Illuminate\Database\Eloquent\Model;
use Kjjdion\LaravelAdminPanel\Traits\DynamicFillable;
use Kjjdion\LaravelAdminPanel\Traits\UserTimezone;

class Role extends Model
{
    use DynamicFillable, UserTimezone;

    // permissions relationship
    public function permissions()
    {
        return $this->belongsToMany(config('lap.models.permission'));
    }
}