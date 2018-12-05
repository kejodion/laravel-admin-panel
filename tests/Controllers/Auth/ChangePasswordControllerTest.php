<?php

namespace Kjjdion\LaravelAdminPanel\Tests\Controllers\Auth;

use Kjjdion\LaravelAdminPanel\Tests\TestCase;

class ChangePasswordControllerTest extends TestCase
{
    public function test_guest_change_redirect()
    {
        $response = $this->get(route('admin.password.change'));
        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_change_form()
    {
        $response = $this->actingAs($this->user_admin)->get(route('admin.password.change'));
        $response->assertViewIs('lap::auth.passwords.change');
    }

    public function test_admin_change_patch()
    {
        $response = $this->actingAs($this->user_admin)->patch(route('admin.password.change'), [
            'current_password' => 'admin123',
            'new_password' => 'admin123',
            'new_password_confirmation' => 'admin123',
        ]);
        $response->assertSessionHas('flash');
        $response->assertJson(['reload_page' => true]);
    }

    public function test_no_perms_change_forbidden()
    {
        $response = $this->actingAs($this->user_no_perms)->get(route('admin.password.change'));
        $response->assertStatus(403);
    }
}