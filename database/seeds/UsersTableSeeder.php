<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('users')->insert([
            'account' => 'xiaoxiao',
            'nickname' => '梦及深海',
            'summary' => '我是简介',
            'password' => md5('123456')
        ]);
    }
}
