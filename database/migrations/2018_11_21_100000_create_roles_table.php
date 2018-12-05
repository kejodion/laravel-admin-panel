<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRolesTable extends Migration
{
    public function up()
    {
        // create table
        Schema::create('roles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->index();
            $table->boolean('admin')->default(false)->index();
            $table->timestamps();
        });

        // create role user relation table
        Schema::create('role_user', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('role_id')->index();
            $table->integer('user_id')->index();
        });

        // create default admin role
        app(config('lap.models.role'))->create([
            'name' => 'Admin',
            'admin' => true,
        ]);

        // create permissions
        app(config('lap.models.permission'))->createGroup('Roles', ['Create Roles', 'Read Roles', 'Update Roles', 'Delete Roles']);
    }

    public function down()
    {
        // drop tables
        Schema::dropIfExists('roles');
        Schema::dropIfExists('role_user');

        // delete permissions
        app(config('lap.models.permission'))->where('group', 'Roles')->delete();
    }
}