<?php

namespace Kjjdion\LaravelAdminPanel\Tests\Middleware;

use Kjjdion\LaravelAdminPanel\Tests\TestCase;

class GuestAdminTest extends TestCase
{
    public function test_guest_has_access()
    {
        $response = $this->get(route('admin.login'));
        $response->assertViewIs('lap::auth.login');
    }

    public function test_admin_gets_redirected()
    {
        $response = $this->actingAs($this->user_admin)->get(route('admin.login'));
        $response->assertRedirect(route('admin.dashboard'));
    }
}