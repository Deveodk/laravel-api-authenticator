<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJwtMagicLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jwt_magic_links', function (Blueprint $table) {
            $table->increments('id');
            $table->text('token');
            $table->text('user_agent');
            $table->ipAddress("ip");
            $table->unsignedInteger('authenticable_id')->nullable();
            $table->string('authenticable_type')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('jwt_magic_links');
    }
}
