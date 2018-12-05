<?php

namespace Kjjdion\LaravelAdminPanel\Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Hash;

class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    public $user_admin;
    public $user_no_perms;

    protected function setUp()
    {
        parent::setUp();

        $this->user_admin = app(config('auth.providers.users.model'))->where('name', 'Admin')->first();

        if (!$this->user_no_perms = app(config('auth.providers.users.model'))->where('name', 'Tester')->first()) {
            $this->user_no_perms = app(config('auth.providers.users.model'))->create([
                'name' => 'Tester',
                'email' => 'tester@example.com',
                'password' => Hash::make('tester123'),
            ]);
        }
    }

    public function createApplication()
    {
        $app = require __DIR__.'/../../../../bootstrap/app.php';
        $app->make(Kernel::class)->bootstrap();

        return $app;
    }
}