<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * 角色表
         */
        Schema::create('roles', function (Blueprint $table) {
            // id
            $table->increments('id');

            // 角色名称
            $table->string('role_name');

            // 角色说明
            $table->string('role_explanation')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('roles');
    }
}
