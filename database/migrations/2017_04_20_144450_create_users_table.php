<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            // id
            $table->increments('id');

            // 账号
            $table->string('account', 16);

            // 昵称
            $table->string('nickname', 16);

            // 密码
            $table->string('password', 64);

            // 头像
            $table->string('avatar');

            // 简介
            $table->string('summary');

            // 账号角色
            $table->integer('role');
            $table->foreign('role')->references('id')->on('roles');

            // 讲师认证状态
            $table->integer('authentication');

            // 账户状态
            $table->integer('status');

            // 证件号码
            $table->string('card_number');

            // 证件照正面
            $table->string('card_front_image');

            // 证件照反面
            $table->string('card_back_image');

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
        Schema::drop('users');
    }
}
