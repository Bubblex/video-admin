<?php

use Illuminate\Database\Seeder;

class CollectsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('collects')->insert([
            'user_id' => 1,
            'article_id' => 4,
            'type' => 1
        ]);

        DB::table('collects')->insert([
            'user_id' => 1,
            'article_id' => 3,
            'type' => 1
        ]);
    }
}
