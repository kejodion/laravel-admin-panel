<?php

namespace Kjjdion\LaravelAdminPanel;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class LapServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // publish install files
        $this->publishes([__DIR__ . '/../config/lap.php' => config_path('lap.php')], 'install'); // config
        $this->publishes([__DIR__ . '/../public' => public_path('lap')], 'install'); // public assets
        $this->publishes([__DIR__ . '/../resources/views/layouts' => resource_path('views/vendor/lap/layouts')], 'install'); // layout views
        $this->publishes([__DIR__ . '/../resources/views/backend' => resource_path('views/vendor/lap/backend')], 'install'); // backend views
        $this->publishes([__DIR__ . '/../resources/stubs/controllers/BackendController.stub' => app_path('Http/Controllers/Admin/BackendController.php')], 'install'); // backend controller

        // publish config
        $this->publishes([__DIR__ . '/../config/lap.php' => config_path('lap.php')], 'config');

        // publish public assets
        $this->publishes([__DIR__ . '/../public' => public_path('lap')], 'public');

        // load & publish views
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'lap');
        $this->publishes([__DIR__ . '/../resources/views' => resource_path('views/vendor/lap')], 'views');

        // publish backend controller
        $this->publishes([__DIR__ . '/../resources/stubs/controllers/BackendController.stub' => app_path('Http/Controllers/Admin/BackendController.php')], 'backend_controller');

        // publish default crud template stubs
        $this->publishes([__DIR__ . '/../resources/stubs/crud/default' => resource_path('stubs/crud/default')], 'crud_stubs');

        // fix database string length error, load & publish migrations
        Schema::defaultStringLength(191);
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->publishes([__DIR__ . '/../database/migrations' => database_path('migrations')], 'migrations');

        // crud commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                Commands\CrudConfig::class,
                Commands\CrudGenerate::class,
            ]);
        }

        // alias middleware
        $this->app['router']->prependMiddlewareToGroup('web', 'Kjjdion\LaravelAdminPanel\Middleware\RestrictDemo');
        $this->app['router']->aliasMiddleware('auth_admin', 'Kjjdion\LaravelAdminPanel\Middleware\AuthAdmin');
        $this->app['router']->aliasMiddleware('guest_admin', 'Kjjdion\LaravelAdminPanel\Middleware\GuestAdmin');
        $this->app['router']->aliasMiddleware('intend_url', 'Kjjdion\LaravelAdminPanel\Middleware\IntendUrl');
        $this->app['router']->aliasMiddleware('not_admin_role', 'Kjjdion\LaravelAdminPanel\Middleware\NotAdminRole');
        $this->app['router']->aliasMiddleware('not_system_doc', 'Kjjdion\LaravelAdminPanel\Middleware\NotSystemDoc');

        // load routes
        $this->loadRoutesFrom(__DIR__ . '/routes.php');

        // gate permissions
        $this->gatePermissions();

        // validator extensions
        $this->validatorExtensions();

        // set config settings
        $this->configSettings();
    }

    public function register()
    {
        // merge config
        $this->mergeConfigFrom(__DIR__ . '/../config/lap.php', 'lap');
    }

    public function gatePermissions()
    {
        Gate::before(function ($user, $permission) {
            if ($user->hasPermission($permission)) {
                return true;
            }
        });
    }

    public function validatorExtensions()
    {
        Validator::extend('current_password', function ($attribute, $value, $parameters, $validator) {
            return Hash::check($value, auth()->user()->password);
        }, 'The current password is invalid.');
    }

    public function configSettings()
    {
        if (Schema::hasTable('settings')) {
            foreach (app(config('lap.models.setting'))->all() as $setting) {
                Config::set('settings.' . $setting->key, $setting->value);
            }
        }
    }
}