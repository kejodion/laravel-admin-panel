<?php

namespace Kjjdion\LaravelAdminPanel\Tests\Controllers\Auth;

use Kjjdion\LaravelAdminPanel\Tests\TestCase;

class ForgotPasswordControllerTest extends TestCase
{
    public function test_guest_email_form()
    {
        $response = $this->get(route('admin.password.request'));
        $response->assertViewIs('lap::auth.passwords.email');
    }

    public function test_guest_email_post()
    {
        $response = $this->post(route('admin.password.email'), [
            'email' => 'admin@example.com',
        ]);
        $response->assertSessionHas('flash');
        $response->assertJson(['reload_page' => true]);
    }

    public function test_admin_email_redirect()
    {
        $response = $this->actingAs($this->user_admin)->get(route('admin.password.request'));
        $response->assertRedirect(route('admin.dashboard'));
    }
}