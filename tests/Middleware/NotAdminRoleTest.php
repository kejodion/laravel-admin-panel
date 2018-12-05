<?php

namespace Kjjdion\LaravelAdminPanel\Tests\Middleware;

use Kjjdion\LaravelAdminPanel\Tests\TestCase;

class NotAdminRoleTest extends TestCase
{
    public function test_admin_role_forbidden()
    {
        $role = app(config('lap.models.role'))->where('admin', true)->first();
        $response = $this->actingAs($this->user_admin)->delete(route('admin.roles.delete', $role->id));
        $response->assertStatus(403);
    }

    public function test_non_admin_role_allowed()
    {
        $role = app(config('lap.models.role'))->create([
            'name' => 'Role Not Admin Test',
        ]);
        $response = $this->actingAs($this->user_admin)->delete(route('admin.roles.delete', $role->id), [
            '_submit' => 'redirect',
        ]);
        $response->assertSessionHas('flash');
        $response->assertRedirect(route('admin.roles'));
    }
}