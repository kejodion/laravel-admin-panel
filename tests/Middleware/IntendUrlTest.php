<?php

namespace Kjjdion\LaravelAdminPanel\Tests\Middleware;

use Kjjdion\LaravelAdminPanel\Tests\TestCase;

class IntendUrlTest extends TestCase
{
    public function test_session_has_url_intended()
    {
        $response = $this->actingAs($this->user_admin)->get(route('admin.roles'));
        $response->assertSessionHas('url.intended', route('admin.roles'));
    }
}