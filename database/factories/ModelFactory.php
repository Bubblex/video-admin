<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

use App\Models\User;

$factory->define(User::class, function () {
    $faker = Faker\Factory::create('zh_CN');
    $initialTime = date('Y-m-d H:i:s', $faker->unixTime($max = 'now'));
    $image = $faker->image($dir = 'public/uploads', $width = 480, $height = 480);
    
    return [
        'account' => $faker->ean8,
        'nickname' => $faker->name,
        'password' => md5('123456'),
        // 'telephone' => $faker->phoneNumber,
        'avatar' => str_replace('public', '', $image),
        'status' => 1,
        'created_at' => $initialTime,
        'updated_at' => $initialTime
    ];
});
