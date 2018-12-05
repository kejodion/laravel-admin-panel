<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocsTable extends Migration
{
    public function up()
    {
        // create table
        Schema::create('docs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type')->index();
            $table->string('title');
            $table->string('slug')->nullable();
            $table->text('content')->nullable();
            $table->boolean('system')->default(false)->index();
            $table->nestedSet();
            $table->timestamps();
        });

        // create system docs
        app(config('lap.models.doc'))->create([
            'type' => 'Index',
            'title' => 'Documentation',
            'content' => 'Welcome to the documentation!',
            'system' => true,
        ]);
        app(config('lap.models.doc'))->create([
            'type' => '404',
            'title' => 'Page Not Found',
            'content' => 'Sorry, the page was not found.',
            'system' => true,
        ]);

        // add permissions
        app(config('lap.models.permission'))->createGroup('Docs', ['Create Docs', 'Read Docs', 'Update Docs', 'Delete Docs']);
    }

    public function down()
    {
        // drop table
        Schema::dropIfExists('docs');

        // delete permissions
        app(config('lap.models.permission'))->where('group', 'Docs')->delete();
    }
}