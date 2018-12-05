<?php

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersColumns extends Migration
{
    public function up()
    {
        // create columns
        Schema::table('users', function (Blueprint $table) {
            $table->index('name');
            $table->string('timezone')->default(config('app.timezone'))->index();
        });

        // create default admin user
        $user = app(config('auth.providers.users.model'))->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('admin123'),
        ]);

        // give default admin user default admin role
        $user->roles()->attach(app(config('lap.models.role'))->where('admin', true)->first()->id);

        // create permissions
        app(config('lap.models.permission'))->createGroup('Users', ['Create Users', 'Read Users', 'Update Users', 'Delete Users']);
    }

    public function down()
    {
        // drop columns
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('admin');
            $table->dropColumn('timezone');
        });

        // delete permissions
        app(config('lap.models.permission'))->where('group', 'Users')->delete();
    }
}