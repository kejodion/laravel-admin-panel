<?php

namespace Kjjdion\LaravelAdminPanel\Tests\Controllers;

use Kjjdion\LaravelAdminPanel\Tests\TestCase;

class BackendControllerTest extends TestCase
{
    /*
     * index
     */

    public function test_guest_index_redirect()
    {
        $response = $this->get(route('admin'));
        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_index_redirect()
    {
        $response = $this->actingAs($this->user_admin)->get(route('admin'));
        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_no_perms_index_redirect()
    {
        $response = $this->actingAs($this->user_admin)->get(route('admin'));
        $response->assertRedirect(route('admin.dashboard'));
    }

    /*
     * dashboard
     */

    public function test_guest_dashboard_redirect()
    {
        $response = $this->get(route('admin.dashboard'));
        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_dashboard_page()
    {
        $response = $this->actingAs($this->user_admin)->get(route('admin.dashboard'));
        $response->assertViewIs('lap::backend.dashboard');
    }

    public function test_no_perms_dashboard_forbidden()
    {
        $response = $this->actingAs($this->user_no_perms)->get(route('admin.dashboard'));
        $response->assertStatus(403);
    }

    /*
     * settings
     */

    public function test_guest_settings_redirect()
    {
        $response = $this->get(route('admin.settings'));
        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_settings_form()
    {
        $response = $this->actingAs($this->user_admin)->get(route('admin.settings'));
        $response->assertViewIs('lap::backend.settings');
    }

    public function test_admin_settings_patch()
    {
        $response = $this->actingAs($this->user_admin)->patch(route('admin.settings'), [
            'example' => 'Hello World Patched',
        ]);
        $response->assertSessionHas('flash');
        $response->assertJson(['reload_page' => true]);
    }

    public function test_no_perms_settings_forbidden()
    {
        $response = $this->actingAs($this->user_no_perms)->get(route('admin.settings'));
        $response->assertStatus(403);
    }
}