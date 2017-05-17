<?php

use Illuminate\Database\Seeder;

use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = factory(User::class, 50)->create();

        DB::table('users')->insert([
            'account' => 'admin',
            'nickname' => '管理员',
            'summary' => '我是简介',
            'password' => md5('123456'),
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            'role_id' => 3
        ]);
    }
}
