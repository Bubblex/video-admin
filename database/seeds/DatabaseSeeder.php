<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        $this->call(RolesTableSeeder::class);
        // $this->call(ArticleTypesTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        // $this->call(ArticlesTableSeeder::class);
        // $this->call(CollectsTableSeeder::class);
        // $this->call(VideosTableSeeder::class);
        // $this->call(MessagesTableSeeder::class);
    }
}
