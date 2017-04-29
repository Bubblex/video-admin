<?php

use Illuminate\Database\Seeder;

class MessagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('messages')->insert([
            'title' => '您已提交讲师认证申请，请耐心等待审核',
            'user_id' => 1
        ]);

        DB::table('messages')->insert([
            'title' => '您已通过讲师认证申请',
            'user_id' => 1
        ]);
    }
}
