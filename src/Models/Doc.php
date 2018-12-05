<?php

namespace Kjjdion\LaravelAdminPanel\Models;

use Illuminate\Database\Eloquent\Model;
use Kalnoy\Nestedset\NodeTrait;
use Kjjdion\LaravelAdminPanel\Traits\DynamicFillable;
use Kjjdion\LaravelAdminPanel\Traits\UserTimezone;
use Parsedown;

class Doc extends Model
{
    use DynamicFillable, UserTimezone, NodeTrait;

    public function markdown()
    {
        return (new Parsedown())->text($this->content);
    }
}