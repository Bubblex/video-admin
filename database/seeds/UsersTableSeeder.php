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
        DB::table('users')->insert([
            'account' => 'xiaoxiao',
            'nickname' => '梦及深海',
            'summary' => '我是简介',
            'password' => md5('123456'),
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('users')->insert([
            'account' => 'yaoyao',
            'nickname' => '贾维斯',
            'summary' => '我是简介',
            'password' => md5('123456'),
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('users')->insert([
            'account' => 'admin',
            'nickname' => '管理员',
            'summary' => '我是简介',
            'password' => md5('123456'),
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            'role_id' => 3
        ]);

        for ($i = 1; $i <= 50; $i++) {
            DB::table('users')->insert([
                'account' => 'xiaoxiao'.$i,
                'nickname' => '梦及深海'.$i,
                'summary' => '我是简介',
                'password' => md5('123456'),
                'authentication' => rand(1, 4),
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s"),
            ]);

            DB::table('users')->insert([
                'account' => 'yaoyao'.$i,
                'nickname' => '贾维斯'.$i,
                'summary' => '我是简介',
                'password' => md5('123456'),
                'authentication' => rand(1, 4),
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s"),
            ]);

            DB::table('users')->insert([
                'account' => 'admin'.$i,
                'nickname' => '管理员'.$i,
                'summary' => '我是简介',
                'password' => md5('123456'),
                'authentication' => rand(1, 4),
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s"),
                'role_id' => 3
            ]);
        }
    }
}
