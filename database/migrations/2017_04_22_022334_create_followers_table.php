<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFollowersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * 关注表
         */
        Schema::create('followers', function (Blueprint $table) {
            // id
            $table->increments('id');

            // 被关注的人
            $table->integer('star')->unsigned();

            // 关注者
            $table->integer('follower')->unsigned();

            // 时间戳
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
        Schema::drop('followers');
    }
}
