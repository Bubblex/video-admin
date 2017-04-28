<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * 文章表
         */
        Schema::create('articles', function (Blueprint $table) {
            // id
            $table->increments('id');

            // 文章标题
            $table->string('title');

            // 文章作者
            $table->integer('author')->unsigned();

            // 文章封面图
            $table->string('cover');

            // 文章简介
            $table->string('summary');

            // 文章内容
            $table->text('content');

            // 文章分类
            $table->integer('type_id');

            // 阅读量
            $table->integer('read_num')->default(0);

            // 文章状态
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
        Schema::drop('articles');
    }
}
