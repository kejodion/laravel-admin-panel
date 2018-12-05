<?php

namespace Kjjdion\LaravelAdminPanel\Tests\Controllers;

use Kjjdion\LaravelAdminPanel\Tests\TestCase;

class ActivityLogControllerTest extends TestCase
{
    public $activity_log;

    protected function setUp()
    {
        parent::setUp();

        $this->activity_log = app(config('lap.models.activity_log'))->create([
            'message' => 'Activity Log Test',
        ]);
    }

    /*
     * index
     */

    public function test_guest_index_redirect()
    {
        $response = $this->get(route('admin.activity_logs'));
        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_index_page()
    {
        $response = $this->actingAs($this->user_admin)->get(route('admin.activity_logs'));
        $response->assertViewIs('lap::activity_logs.index');
    }

    public function test_no_perms_index_forbidden()
    {
        $response = $this->actingAs($this->user_no_perms)->get(route('admin.activity_logs'));
        $response->assertStatus(403);
    }

    /*
     * read
     */

    public function test_admin_read_page()
    {
        $response = $this->actingAs($this->user_admin)->get(route('admin.activity_logs.read', $this->activity_log->id));
        $response->assertViewIs('lap::activity_logs.read');
    }

    public function test_guest_read_redirect()
    {
        $response = $this->get(route('admin.activity_logs.read', $this->activity_log->id));
        $response->assertRedirect(route('admin.login'));
    }

    public function test_no_perms_read_forbidden()
    {
        $response = $this->actingAs($this->user_no_perms)->get(route('admin.activity_logs.read', $this->activity_log->id));
        $response->assertStatus(403);
    }
}