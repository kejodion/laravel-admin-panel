<?php

namespace Kjjdion\LaravelAdminPanel\Tests\Middleware;

use Kjjdion\LaravelAdminPanel\Tests\TestCase;

use Illuminate\Support\Facades\Config;

class RestrictDemoTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();

        Config::set('lap.demo.enabled', true);
    }

    public function test_demo_enabled()
    {
        $this->assertTrue(config('lap.demo.enabled'));
    }

    public function test_demo_method_allowed()
    {
        $response = $this->actingAs($this->user_admin)->get(route('admin.roles.create'));
        $response->assertViewIs('lap::roles.create');
    }

    public function test_demo_route_allowed()
    {
        $response = $this->actingAs($this->user_admin)->post(route('admin.logout'));
        $response->assertRedirect(route('admin.login'));
    }

    protected function tearDown()
    {
        parent::tearDown();

        Config::set('lap.demo.enabled', false);
    }
}