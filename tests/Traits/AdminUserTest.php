<?php

namespace Kjjdion\LaravelAdminPanel\Tests\Traits;

use Kjjdion\LaravelAdminPanel\Tests\TestCase;

class AdminUserTest extends TestCase
{
    public function test_has_permission()
    {
        $has_permission = $this->user_admin->hasPermission('Access Admin Panel');
        $this->assertTrue($has_permission);
    }

    public function test_doesnt_have_permission()
    {
        $has_permission = $this->user_no_perms->hasPermission('Access Admin Panel');
        $this->assertFalse($has_permission);
    }
}