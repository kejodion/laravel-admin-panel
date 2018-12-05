<?php

namespace Kjjdion\LaravelAdminPanel\Tests\Controllers\Auth;

use Kjjdion\LaravelAdminPanel\Tests\TestCase;

use Illuminate\Support\Facades\Password;

class ResetPasswordControllerTest extends TestCase
{
    public $token;

    protected function setUp()
    {
        parent::setUp();

        $this->token = Password::getRepository()->create($this->user_admin);
    }

    public function test_guest_reset_form()
    {
        $response = $this->get(route('admin.password.reset', $this->token));
        $response->assertViewIs('lap::auth.passwords.reset');
    }

    public function test_guest_reset_post()
    {
        $response = $this->post(route('admin.password.reset'), [
            'email' => 'admin@example.com',
            'password' => 'admin123',
            'password_confirmation' => 'admin123',
            'token' => $this->token,
        ]);
        $response->assertSessionHas('flash');
        $response->assertJson(['redirect' => route('admin.dashboard')]);
    }

    public function test_admin_reset_redirect()
    {
        $response = $this->actingAs($this->user_admin)->get(route('admin.password.reset'));
        $response->assertRedirect(route('admin.dashboard'));
    }
}