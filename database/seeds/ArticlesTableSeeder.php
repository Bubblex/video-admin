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
        for ($i = 1; $i <= 50; $i++) {
            DB::table('articles')->insert([
                'title' => '文章标题'.$i,
                'author' => rand(1, 50),
                'cover' => '/',
                'summary' => '文章简介'.$i,
                'content' => '文章内容'.$i,
                'type_id' => rand(1, 3),
                'read_num' => rand(1, 10000),
                'status' => rand(1, 2),
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s"),
            ]);
        }
    }
}
