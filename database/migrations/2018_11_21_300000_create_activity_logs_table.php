<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivityLogsTable extends Migration
{
    public function up()
    {
        // create table
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->nullable()->index();
            $table->integer('model_id')->nullable()->index();
            $table->string('model_class')->nullable();
            $table->string('message')->index();
            $table->text('data')->nullable();
            $table->timestamp('created_at')->index();
        });

        // add permissions
        app(config('lap.models.permission'))->createGroup('Activity Logs', ['Read Activity Logs']);
    }

    public function down()
    {
        // drop table
        Schema::dropIfExists('activity_logs');

        // delete permissions
        app(config('lap.models.permission'))->where('group', 'Activity Logs')->delete();
    }
}