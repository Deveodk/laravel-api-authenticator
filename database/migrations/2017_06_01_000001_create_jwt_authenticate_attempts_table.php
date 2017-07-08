<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJwtAuthenticateAttemptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jwt_authenticate_attempts', function (Blueprint $table) {
            $table->increments('id');
            $table->text('user_agent');
            $table->string('email');
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
        Schema::drop('jwt_tokens');
    }
}
