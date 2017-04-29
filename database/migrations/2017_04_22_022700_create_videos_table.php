<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('videos', function (Blueprint $table) {
            // id
            $table->increments('id');

            // 视频标题
            $table->string('title');

            // 视频作者
            $table->integer('author')->unsigned();

            // 视频封面图
            $table->string('cover');

            // 视频简介
            $table->string('summary');

            // 视频地址
            $table->string('video_url');

            // 播放量
            $table->integer('play_num')->default(0);

            // 视频状态
            $table->integer('status')->default(0);

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
        Schema::drop('videos');
    }
}
