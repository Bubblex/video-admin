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
        /**
         * 用户表
         */
        Schema::create('users', function (Blueprint $table) {
            // id
            $table->increments('id');

            // 账号
            $table->string('account', 16)->unique();

            // 昵称
            $table->string('nickname', 16);

            // 密码
            $table->string('password', 64);

            // 头像
            $table->string('avatar')->nullable();

            // 简介
            $table->string('summary')->nullable();

            // 账号角色
            $table->integer('role')->unsigned()->default(1);

            // 讲师认证状态
            $table->integer('authentication')->default(1);

            // 账户状态
            $table->integer('status')->default(1);

            // 证件号码
            $table->string('card_number')->nullable();

            // 证件照正面
            $table->string('card_front_image')->nullable();

            // 证件照反面
            $table->string('card_back_image')->nullable();

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
