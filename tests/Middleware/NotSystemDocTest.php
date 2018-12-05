<?php

namespace Kjjdion\LaravelAdminPanel\Tests\Middleware;

use Kjjdion\LaravelAdminPanel\Tests\TestCase;

class NotSystemDocTest extends TestCase
{
    public function test_system_doc_forbidden()
    {
        $doc = app(config('lap.models.doc'))->where('system', true)->first();
        $response = $this->actingAs($this->user_admin)->delete(route('admin.docs.delete', $doc->id));
        $response->assertStatus(403);
    }

    public function test_non_system_doc_allowed()
    {
        $doc = app(config('lap.models.doc'))->create([
            'type' => 'Page',
            'title' => 'Doc Not Admin Test',
        ]);
        $response = $this->actingAs($this->user_admin)->delete(route('admin.docs.delete', $doc->id), [
            '_submit' => 'redirect',
        ]);
        $response->assertSessionHas('flash');
        $response->assertRedirect(route('admin.docs'));
    }
}