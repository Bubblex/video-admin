<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            // id
            $table->increments('id');

            // 消息标题
            $table->string('title');

            // 消息内容
            $table->string('content')->nullable();

            // 用户
            $table->integer('user_id')->unsigned();

            // 状态 1: 未读 2: 已读
            $table->integer('status')->default(1);

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
        Schema::drop('messages');
    }
}
