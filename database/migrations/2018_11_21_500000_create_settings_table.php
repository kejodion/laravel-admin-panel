<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSettingsTable extends Migration
{
    public function up()
    {
        // create table
        Schema::create('settings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('key')->index();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // create example setting
        app(config('lap.models.setting'))->create([
            'key' => 'example',
            'value' => 'Hello World',
        ]);

        // add permissions
        app(config('lap.models.permission'))->createGroup('Settings', ['Update Settings']);
    }

    public function down()
    {
        // drop table
        Schema::dropIfExists('settings');

        // delete permissions
        app(config('lap.models.permission'))->where('group', 'Settings')->delete();
    }
}