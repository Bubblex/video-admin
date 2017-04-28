<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCollectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('collects', function (Blueprint $table) {
            // id
            $table->increments('id');

            // 收藏人
            $table->integer('user_id')->unsigned()->nullable();

            // 文章 id
            $table->integer('article_id')->unsigned()->nullable();

            // 视频 id
            $table->integer('video_id')->unsigned()->nullable();

            // 收藏的类型
            $table->integer('type');

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
        Schema::drop('collects');
    }
}
