<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJwtBlacklistTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jwt_blacklists', function (Blueprint $table) {
            $table->increments('id');
            $table->string('key');
            $table->text('token');
            $table->text('user_agent');
            $table->ipAddress("ip");
            $table->unsignedInteger('authenticable_id')->nullable();
            $table->string('authenticable_type')->nullable();
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('jwt_blacklists');
    }
}
