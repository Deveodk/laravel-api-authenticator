<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJwtAuthenticateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jwt_authenticates', function (Blueprint $table) {
            $table->increments('id');
            $table->text('user_agent');
            $table->text('token');
            $table->ipAddress("ip");
            $table->unsignedInteger('authenticable_id');
            $table->string('authenticable_type');
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
        Schema::drop('jwt_tokens');
    }
}
