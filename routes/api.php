<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['namespace' => 'Account'], function() {
    Route::group(['prefix' => 'account'], function() {
        // 注册接口
        Route::post('register', 'UserController@postRegister');

        // 登录
        Route::post('login', 'UserController@postLogin');

        Route::group(['middleware' => ['checkToken']], function() {
            // 重置密码
            Route::post('reset', 'UserController@postReset');

            // 更新个人信息
            Route::post('update', 'UserController@updateUserInfo');
        });
    });

    Route::group(['middleware' => ['checkToken']], function() {
        // 获取用户完整信息
        Route::post('user/info', 'UserController@getUserInfo');

        // 申请成为讲师
        Route::post('apply/lecturer', 'UserController@applyLecturer');
    });

    Route::post('user/basic', 'UserController@getUserInfoById');

    Route::post('stars', 'UserController@getUserStars');
});
