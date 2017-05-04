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
        for ($i = 1; $i <= 50; $i++) {
            DB::table('videos')->insert([
                'title' => '视频标题'.$i,
                'author' => rand(1, 50),
                'cover' => '/'.$i,
                'summary' => '视频简介'.$i,
                'video_url' => '/视频地址'.$i,
                'status' => rand(1, 2),
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s"),
            ]);
        }
    }
}
