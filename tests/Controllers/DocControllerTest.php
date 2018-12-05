<?php

namespace Kjjdion\LaravelAdminPanel\Tests\Controllers;

use Kjjdion\LaravelAdminPanel\Tests\TestCase;

class DocControllerTest extends TestCase
{
    public $doc;

    protected function setUp()
    {
        parent::setUp();

        $this->doc = app(config('lap.models.doc'))->create([
            'type' => 'Page',
            'title' => 'Doc Test',
        ]);
    }

    /*
     * frontend
     */

    public function test_frontend_page()
    {
        $response = $this->get(route('docs'));
        $response->assertViewIs('lap::layouts.docs');
    }

    /*
     * index
     */
    
    public function test_guest_index_redirect()
    {
        $response = $this->get(route('admin.docs'));
        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_index_page()
    {
        $response = $this->actingAs($this->user_admin)->get(route('admin.docs'));
        $response->assertViewIs('lap::docs.index');
    }

    public function test_no_perms_index_forbidden()
    {
        $response = $this->actingAs($this->user_no_perms)->get(route('admin.docs'));
        $response->assertStatus(403);
    }

    /*
     * create
     */

    public function test_guest_create_redirect()
    {
        $response = $this->get(route('admin.docs.create'));
        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_create_form()
    {
        $response = $this->actingAs($this->user_admin)->get(route('admin.docs.create'));
        $response->assertViewIs('lap::docs.create');
    }

    public function test_admin_create_post_reload()
    {
        $response = $this->actingAs($this->user_admin)->post(route('admin.docs.create'), [
            'type' => 'Page',
            'title' => 'Doc Test Post Reload',
            '_submit' => 'reload_page',
        ]);
        $response->assertSessionHas('flash');
        $response->assertJson(['reload_page' => true]);
    }

    public function test_admin_create_post_redirect()
    {
        $response = $this->actingAs($this->user_admin)->post(route('admin.docs.create'), [
            'type' => 'Page',
            'title' => 'Doc Test Post Redirect',
            '_submit' => 'redirect',
        ]);
        $response->assertSessionHas('flash');
        $response->assertJson(['redirect' => route('admin.docs')]);
    }

    public function test_no_perms_create_forbidden()
    {
        $response = $this->actingAs($this->user_no_perms)->get(route('admin.docs.create'));
        $response->assertStatus(403);
    }

    /*
     * read
     */

    public function test_guest_read_redirect()
    {
        $response = $this->get(route('admin.docs.read', $this->doc->id));
        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_read_page()
    {
        $response = $this->actingAs($this->user_admin)->get(route('admin.docs.read', $this->doc->id));
        $response->assertViewIs('lap::docs.read');
    }

    public function test_no_perms_read_forbidden()
    {
        $response = $this->actingAs($this->user_no_perms)->get(route('admin.docs.read', $this->doc->id));
        $response->assertStatus(403);
    }

    /*
     * update
     */

    public function test_guest_update_redirect()
    {
        $response = $this->get(route('admin.docs.update', $this->doc->id));
        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_update_form()
    {
        $response = $this->actingAs($this->user_admin)->get(route('admin.docs.update', $this->doc->id));
        $response->assertViewIs('lap::docs.update');
    }

    public function test_admin_update_patch_reload()
    {
        $response = $this->actingAs($this->user_admin)->patch(route('admin.docs.update', $this->doc->id), [
            'type' => 'Page',
            'title' => 'Doc Test Patch Reload',
            '_submit' => 'reload_page',
        ]);
        $response->assertSessionHas('flash');
        $response->assertJson(['reload_page' => true]);
    }

    public function test_admin_update_patch_redirect()
    {
        $response = $this->actingAs($this->user_admin)->patch(route('admin.docs.update', $this->doc->id), [
            'type' => 'Page',
            'title' => 'Doc Test Patch Redirect',
            '_submit' => 'redirect',
        ]);
        $response->assertSessionHas('flash');
        $response->assertJson(['redirect' => route('admin.docs')]);
    }

    public function test_no_perms_update_forbidden()
    {
        $response = $this->actingAs($this->user_no_perms)->get(route('admin.docs.update', $this->doc->id));
        $response->assertStatus(403);
    }

    /*
     * move
     */

    public function test_guest_move_redirect()
    {
        $response = $this->patch(route('admin.docs.move', $this->doc->id));
        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_move_patch()
    {
        $response = $this->actingAs($this->user_admin)->patch(route('admin.docs.move', $this->doc->id), [
            '_submit' => 'up',
        ]);
        $response->assertJson(['reload_datatables' => true]);
    }

    public function test_no_perms_move_forbidden()
    {
        $response = $this->actingAs($this->user_no_perms)->patch(route('admin.docs.move', $this->doc->id));
        $response->assertStatus(403);
    }
    
    /*
     * delete
     */

    public function test_guest_delete_redirect()
    {
        $response = $this->delete(route('admin.docs.delete', $this->doc->id));
        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_delete_reload()
    {
        $doc = app(config('lap.models.doc'))->create([
            'type' => 'Page',
            'title' => 'Doc Test Delete Reload',
        ]);
        $response = $this->actingAs($this->user_admin)->delete(route('admin.docs.delete', $doc->id), [
            '_submit' => 'reload_datatables',
        ]);
        $response->assertJson([
            'flash' => true,
            'reload_datatables' => true,
        ]);
    }

    public function test_admin_delete_redirect()
    {
        $doc = app(config('lap.models.doc'))->create([
            'type' => 'Page',
            'title' => 'Doc Test Delete Redirect',
        ]);
        $response = $this->actingAs($this->user_admin)->delete(route('admin.docs.delete', $doc->id), [
            '_submit' => 'redirect',
        ]);
        $response->assertSessionHas('flash');
        $response->assertRedirect(route('admin.docs'));
    }

    public function test_no_perms_delete_forbidden()
    {
        $response = $this->actingAs($this->user_no_perms)->delete(route('admin.docs.delete', $this->doc->id));
        $response->assertStatus(403);
    }
}