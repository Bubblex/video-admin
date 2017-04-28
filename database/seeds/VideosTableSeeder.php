<?php

use Illuminate\Database\Seeder;

class VideosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('videos')->insert([
            'title' => '视频标题1',
            'author' => 1,
            'cover' => '/',
            'summary' => '视频封面',
            'video_url' => '/',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('videos')->insert([
            'title' => '视频标题2',
            'author' => 1,
            'cover' => '/',
            'summary' => '视频封面',
            'video_url' => '/',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
        ]);

        DB::table('videos')->insert([
            'title' => '视频标题3',
            'author' => 1,
            'cover' => '/',
            'summary' => '视频封面',
            'video_url' => '/',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
        ]);
    }
}
