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

    // 获取用户基础数据
    Route::post('user/basic', 'UserController@getUserInfoById');

    // 获取用户关注列表
    Route::post('stars', 'UserController@getUserStars');

    // 获取用户粉丝列表
    Route::post('followers', 'UserController@getUserFollowers');

    // 关注用户
    Route::post('follow', 'UserController@followUser');

    // 取消关注用户
    Route::post('unfollow', 'UserController@unfollowUser');

    // 文件上传
    Route::post('upload', 'UserController@uploadFile');

    // 获取文章类型
    Route::post('article/type', 'UserController@getArticleType');

    // 获取文章列表
    Route::post('article/list', 'UserController@getArticleList');

    // 获取文章详情
    Route::post('article/detail', 'UserController@getArticleDetail');
});
