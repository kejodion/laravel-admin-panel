<?php

namespace Kjjdion\LaravelAdminPanel\Tests\Middleware;

use Kjjdion\LaravelAdminPanel\Tests\TestCase;

class AuthAdminTest extends TestCase
{
    public function test_guest_gets_redirected()
    {
        $response = $this->get(route('admin.dashboard'));
        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_has_access()
    {
        $response = $this->actingAs($this->user_admin)->get(route('admin.dashboard'));
        $response->assertViewIs('lap::backend.dashboard');
    }
}