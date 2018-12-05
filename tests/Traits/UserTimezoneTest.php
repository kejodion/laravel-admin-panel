<?php

namespace Kjjdion\LaravelAdminPanel\Tests\Traits;

use Carbon\Carbon;
use Kjjdion\LaravelAdminPanel\Tests\TestCase;

class UserTimezoneTest extends TestCase
{
    public function test_in_user_timezone()
    {
        $this->user_no_perms->timezone = 'America/Toronto';
        $this->actingAs($this->user_no_perms);
        $role = app(config('lap.models.role'))->create(['name' => 'Role Test Timezone']);
        $this->assertEquals(Carbon::now()->tz($this->user_no_perms->timezone)->toDateTimeString(), $role->created_at);
    }
}