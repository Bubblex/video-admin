<?php

use Illuminate\Database\Seeder;

class ArticleTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('article_types')->insert([
            'type_name' => '邮票'
        ]);

        DB::table('article_types')->insert([
            'type_name' => '货币'
        ]);

        DB::table('article_types')->insert([
            'type_name' => '电话卡'
        ]);
    }
}
