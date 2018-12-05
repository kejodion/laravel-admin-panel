<?php

namespace Kjjdion\LaravelAdminPanel\Tests\Controllers\Auth;

use Kjjdion\LaravelAdminPanel\Tests\TestCase;

class ProfileControllerTest extends TestCase
{
    public function test_guest_profile_redirect()
    {
        $response = $this->get(route('admin.profile'));
        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_profile_form()
    {
        $response = $this->actingAs($this->user_admin)->get(route('admin.profile'));
        $response->assertViewIs('lap::auth.profile');
    }

    public function test_admin_profile_patch()
    {
        $response = $this->actingAs($this->user_admin)->patch(route('admin.profile'), [
            'name' => 'Admin',
            'email' => 'admin@example.com',
        ]);
        $response->assertSessionHas('flash');
        $response->assertJson(['reload_page' => true]);
    }

    public function test_no_perms_profile_forbidden()
    {
        $response = $this->actingAs($this->user_no_perms)->get(route('admin.profile'));
        $response->assertStatus(403);
    }
}