<?php

namespace Kjjdion\LaravelAdminPanel\Tests\Controllers;

use Kjjdion\LaravelAdminPanel\Tests\TestCase;

use Illuminate\Support\Facades\Hash;

class UserControllerTest extends TestCase
{
    public $user;

    protected function setUp()
    {
        parent::setUp();

        if (!$this->user = app(config('auth.providers.users.model'))->where('name', 'User Test')->first()) {
            $this->user = app(config('auth.providers.users.model'))->create([
                'name' => 'User Test',
                'email' => 'usertest@example.com',
                'password' => Hash::make('usertest123'),
            ]);
        }
    }

    /*
     * index
     */

    public function test_guest_index_redirect()
    {
        $response = $this->get(route('admin.users'));
        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_index_page()
    {
        $response = $this->actingAs($this->user_admin)->get(route('admin.users'));
        $response->assertViewIs('lap::users.index');
    }

    public function test_no_perms_index_forbidden()
    {
        $response = $this->actingAs($this->user_no_perms)->get(route('admin.users'));
        $response->assertStatus(403);
    }

    /*
     * create
     */

    public function test_guest_create_redirect()
    {
        $response = $this->get(route('admin.users.create'));
        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_create_form()
    {
        $response = $this->actingAs($this->user_admin)->get(route('admin.users.create'));
        $response->assertViewIs('lap::users.create');
    }

    public function test_admin_create_post_reload()
    {
        $response = $this->actingAs($this->user_admin)->post(route('admin.users.create'), [
            'name' => 'User Test Post Reload',
            'email' => 'usertestpostreload@example.com',
            'password' => 'usertestpostreload123',
            'password_confirmation' => 'usertestpostreload123',
            '_submit' => 'reload_page',
        ]);
        $response->assertSessionHas('flash');
        $response->assertJson(['reload_page' => true]);
    }

    public function test_admin_create_post_redirect()
    {
        $response = $this->actingAs($this->user_admin)->post(route('admin.users.create'), [
            'name' => 'User Test Post Redirect',
            'email' => 'usertestpostredirect@example.com',
            'password' => 'usertestpostredirect123',
            'password_confirmation' => 'usertestpostredirect123',
            '_submit' => 'redirect',
        ]);
        $response->assertSessionHas('flash');
        $response->assertJson(['redirect' => route('admin.users')]);
    }

    public function test_no_perms_create_forbidden()
    {
        $response = $this->actingAs($this->user_no_perms)->get(route('admin.users.create'));
        $response->assertStatus(403);
    }

    /*
     * read
     */

    public function test_guest_read_redirect()
    {
        $response = $this->get(route('admin.users.read', $this->user->id));
        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_read_page()
    {
        $response = $this->actingAs($this->user_admin)->get(route('admin.users.read', $this->user->id));
        $response->assertViewIs('lap::users.read');
    }

    public function test_no_perms_read_forbidden()
    {
        $response = $this->actingAs($this->user_no_perms)->get(route('admin.users.read', $this->user->id));
        $response->assertStatus(403);
    }

    /*
     * update
     */

    public function test_guest_update_redirect()
    {
        $response = $this->get(route('admin.users.update', $this->user->id));
        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_update_form()
    {
        $response = $this->actingAs($this->user_admin)->get(route('admin.users.update', $this->user->id));
        $response->assertViewIs('lap::users.update');
    }

    public function test_admin_update_patch_reload()
    {
        $response = $this->actingAs($this->user_admin)->patch(route('admin.users.update', $this->user->id), [
            'name' => 'User Test Patch Reload',
            'email' => 'usertestpatchreload@example.com',
            '_submit' => 'reload_page',
        ]);
        $response->assertSessionHas('flash');
        $response->assertJson(['reload_page' => true]);
    }

    public function test_admin_update_patch_redirect()
    {
        $response = $this->actingAs($this->user_admin)->patch(route('admin.users.update', $this->user->id), [
            'name' => 'User Test Patch Redirect',
            'email' => 'usertestpatchredirect@example.com',
            '_submit' => 'redirect',
        ]);
        $response->assertSessionHas('flash');
        $response->assertJson(['redirect' => route('admin.users')]);
    }

    public function test_no_perms_update_forbidden()
    {
        $response = $this->actingAs($this->user_no_perms)->get(route('admin.users.update', $this->user->id));
        $response->assertStatus(403);
    }

    /*
     * password
     */

    public function test_guest_password_redirect()
    {
        $response = $this->get(route('admin.users.password', $this->user->id));
        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_password_form()
    {
        $response = $this->actingAs($this->user_admin)->get(route('admin.users.password', $this->user->id));
        $response->assertViewIs('lap::users.password');
    }

    public function test_admin_password_patch_reload()
    {
        $response = $this->actingAs($this->user_admin)->patch(route('admin.users.password', $this->user->id), [
            'new_password' => 'usertestpasswordreload123',
            'new_password_confirmation' => 'usertestpasswordreload123',
            '_submit' => 'reload_page',
        ]);
        $response->assertSessionHas('flash');
        $response->assertJson(['reload_page' => true]);
    }

    public function test_admin_password_patch_redirect()
    {
        $response = $this->actingAs($this->user_admin)->patch(route('admin.users.password', $this->user->id), [
            'new_password' => 'usertestpasswordredirect123',
            'new_password_confirmation' => 'usertestpasswordredirect123',
            '_submit' => 'redirect',
        ]);
        $response->assertSessionHas('flash');
        $response->assertJson(['redirect' => route('admin.users')]);
    }

    public function test_no_perms_password_forbidden()
    {
        $response = $this->actingAs($this->user_no_perms)->get(route('admin.users.password', $this->user->id));
        $response->assertStatus(403);
    }

    /*
     * delete
     */

    public function test_guest_delete_redirect()
    {
        $response = $this->delete(route('admin.users.delete', $this->user->id));
        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_delete_reload()
    {
        $user = app(config('auth.providers.users.model'))->create([
            'name' => 'User Test Delete Reload',
            'email' => 'usertestdeletereload@example.com',
            'password' => Hash::make('usertestdeletereload123'),
        ]);
        $response = $this->actingAs($this->user_admin)->delete(route('admin.users.delete', $user->id), [
            '_submit' => 'reload_datatables',
        ]);
        $response->assertJson([
            'flash' => true,
            'reload_datatables' => true,
        ]);
    }

    public function test_admin_delete_redirect()
    {
        $user = app(config('auth.providers.users.model'))->create([
            'name' => 'User Test Delete Redirect',
            'email' => 'usertestdeleteredirect@example.com',
            'password' => Hash::make('usertestdeleteredirect123'),
        ]);
        $response = $this->actingAs($this->user_admin)->delete(route('admin.users.delete', $user->id), [
            '_submit' => 'redirect',
        ]);
        $response->assertSessionHas('flash');
        $response->assertRedirect(route('admin.users'));
    }

    public function test_no_perms_delete_forbidden()
    {
        $response = $this->actingAs($this->user_no_perms)->delete(route('admin.users.delete', $this->user->id));
        $response->assertStatus(403);
    }
}