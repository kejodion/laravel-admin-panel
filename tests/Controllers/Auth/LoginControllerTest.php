<?php

namespace Kjjdion\LaravelAdminPanel\Tests\Controllers\Auth;

use Kjjdion\LaravelAdminPanel\Tests\TestCase;

class LoginControllerTest extends TestCase
{
    /*
     * login
     */

    public function test_guest_login_form()
    {
        $response = $this->get(route('admin.login'));
        $response->assertViewIs('lap::auth.login');
    }

    public function test_guest_login_post()
    {
        $response = $this->post(route('admin.login'), [
            'email' => 'admin@example.com',
            'password' => 'admin123',
        ]);
        $response->assertJson(['redirect' => route('admin.dashboard')]);
    }

    public function test_admin_login_redirect()
    {
        $response = $this->actingAs($this->user_admin)->get(route('admin.login'));
        $response->assertRedirect(route('admin.dashboard'));
    }

    /*
     * logout
     */

    public function test_admin_logout_post()
    {
        $response = $this->actingAs($this->user_admin)->post(route('admin.logout'));
        $response->assertRedirect(route('admin.login'));
    }
}