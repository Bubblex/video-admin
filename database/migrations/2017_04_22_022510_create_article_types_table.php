<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticleTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * 文章类型表
         */
        Schema::create('article_types', function (Blueprint $table) {
            // id
            $table->increments('id');

            // 分类名称
            $table->string('type_name');

            // 分类说明
            $table->string('type_explanation');

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
        Schema::drop('article_types');
    }
}
