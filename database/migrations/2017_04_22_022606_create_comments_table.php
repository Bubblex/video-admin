<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            // id
            $table->increments('id');

            // 评论者
            $table->integer('user_id')->unsigned();

            // 评论文章的 id
            $table->integer('article_id')->unsigned()->nullable();

            // 评论视频的 id
            $table->integer('video_id')->unsigned()->nullable();

            // 回复对象
            $table->integer('reply_id')->unsigned()->nullable();

            // 回复内容
            $table->string('content');

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
        Schema::drop('comments');
    }
}
