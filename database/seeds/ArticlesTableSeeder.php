<?php

use Illuminate\Database\Seeder;

class ArticlesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('articles')->insert([
            'title' => '文章标题',
            'author' => 1,
            'cover' => '/',
            'summary' => '文章简介',
            'content' => '文章内容',
            'type_id' => 1,
            'read_num' => 100,
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('articles')->insert([
            'title' => '文章标题2',
            'author' => 1,
            'cover' => '/',
            'summary' => '文章简介',
            'content' => '文章内容',
            'type_id' => 2,
            'read_num' => 100,
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('articles')->insert([
            'title' => '文章标题3',
            'author' => 2,
            'cover' => '/',
            'summary' => '文章简介',
            'content' => '文章内容',
            'type_id' => 1,
            'read_num' => 100,
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('articles')->insert([
            'title' => '文章标题4',
            'author' => 2,
            'cover' => '/',
            'summary' => '文章简介',
            'content' => '文章内容',
            'type_id' => 1,
            'read_num' => 100,
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
        ]);
    }
}
