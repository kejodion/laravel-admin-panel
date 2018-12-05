<?php

namespace Kjjdion\LaravelAdminPanel\Tests\Controllers;

use Kjjdion\LaravelAdminPanel\Tests\TestCase;

class RoleControllerTest extends TestCase
{
    public $role;

    protected function setUp()
    {
        parent::setUp();

        $this->role = app(config('lap.models.role'))->create([
            'name' => 'Role Test',
        ]);
    }

    /*
     * index
     */
    
    public function test_guest_index_redirect()
    {
        $response = $this->get(route('admin.roles'));
        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_index_page()
    {
        $response = $this->actingAs($this->user_admin)->get(route('admin.roles'));
        $response->assertViewIs('lap::roles.index');
    }

    public function test_no_perms_index_forbidden()
    {
        $response = $this->actingAs($this->user_no_perms)->get(route('admin.roles'));
        $response->assertStatus(403);
    }

    /*
     * create
     */

    public function test_guest_create_redirect()
    {
        $response = $this->get(route('admin.roles.create'));
        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_create_form()
    {
        $response = $this->actingAs($this->user_admin)->get(route('admin.roles.create'));
        $response->assertViewIs('lap::roles.create');
    }

    public function test_admin_create_post_reload()
    {
        $response = $this->actingAs($this->user_admin)->post(route('admin.roles.create'), [
            'name' => 'Role Test Post Reload',
            '_submit' => 'reload_page',
        ]);
        $response->assertSessionHas('flash');
        $response->assertJson(['reload_page' => true]);
    }

    public function test_admin_create_post_redirect()
    {
        $response = $this->actingAs($this->user_admin)->post(route('admin.roles.create'), [
            'name' => 'Role Test Post Redirect',
            '_submit' => 'redirect',
        ]);
        $response->assertSessionHas('flash');
        $response->assertJson(['redirect' => route('admin.roles')]);
    }

    public function test_no_perms_create_forbidden()
    {
        $response = $this->actingAs($this->user_no_perms)->get(route('admin.roles.create'));
        $response->assertStatus(403);
    }

    /*
     * read
     */

    public function test_guest_read_redirect()
    {
        $response = $this->get(route('admin.roles.read', $this->role->id));
        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_read_page()
    {
        $response = $this->actingAs($this->user_admin)->get(route('admin.roles.read', $this->role->id));
        $response->assertViewIs('lap::roles.read');
    }

    public function test_no_perms_read_forbidden()
    {
        $response = $this->actingAs($this->user_no_perms)->get(route('admin.roles.read', $this->role->id));
        $response->assertStatus(403);
    }

    /*
     * update
     */

    public function test_guest_update_redirect()
    {
        $response = $this->get(route('admin.roles.update', $this->role->id));
        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_update_form()
    {
        $response = $this->actingAs($this->user_admin)->get(route('admin.roles.update', $this->role->id));
        $response->assertViewIs('lap::roles.update');
    }

    public function test_admin_update_patch_reload()
    {
        $response = $this->actingAs($this->user_admin)->patch(route('admin.roles.update', $this->role->id), [
            'name' => 'Role Test Patch Reload',
            '_submit' => 'reload_page',
        ]);
        $response->assertSessionHas('flash');
        $response->assertJson(['reload_page' => true]);
    }

    public function test_admin_update_patch_redirect()
    {
        $response = $this->actingAs($this->user_admin)->patch(route('admin.roles.update', $this->role->id), [
            'name' => 'Role Test Patch Redirect',
            '_submit' => 'redirect',
        ]);
        $response->assertSessionHas('flash');
        $response->assertJson(['redirect' => route('admin.roles')]);
    }

    public function test_no_perms_update_forbidden()
    {
        $response = $this->actingAs($this->user_no_perms)->get(route('admin.roles.update', $this->role->id));
        $response->assertStatus(403);
    }
    
    /*
     * delete
     */

    public function test_guest_delete_redirect()
    {
        $response = $this->delete(route('admin.roles.delete', $this->role->id));
        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_delete_reload()
    {
        $role = app(config('lap.models.role'))->create([
            'name' => 'Role Test Delete Reload',
        ]);
        $response = $this->actingAs($this->user_admin)->delete(route('admin.roles.delete', $role->id), [
            '_submit' => 'reload_datatables',
        ]);
        $response->assertJson([
            'flash' => true,
            'reload_datatables' => true,
        ]);
    }

    public function test_admin_delete_redirect()
    {
        $role = app(config('lap.models.role'))->create([
            'name' => 'Role Test Delete Redirect',
        ]);
        $response = $this->actingAs($this->user_admin)->delete(route('admin.roles.delete', $role->id), [
            '_submit' => 'redirect',
        ]);
        $response->assertSessionHas('flash');
        $response->assertRedirect(route('admin.roles'));
    }

    public function test_no_perms_delete_forbidden()
    {
        $response = $this->actingAs($this->user_no_perms)->delete(route('admin.roles.delete', $this->role->id));
        $response->assertStatus(403);
    }
}