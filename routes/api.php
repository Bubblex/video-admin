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

    Route::post('admin/login', 'UserController@adminLogin');

    Route::group(['middleware' => ['checkToken']], function() {
        // 获取用户完整信息
        Route::post('user/info', 'UserController@getUserInfo');

        // 申请成为讲师
        Route::post('apply/lecturer', 'UserController@applyLecturer');

        // 收藏文章
        Route::post('article/collect', 'UserController@collectArticle');

        // 取消收藏文章
        Route::post('article/cancel', 'UserController@cancelCollectArticle');

        // 发布 / 修改文章
        Route::post('article/release', 'UserController@releaseArticle');

        // 删除文章
        Route::post('article/delete', 'UserController@deleteArticle');

        // 收藏视频
        Route::post('video/collect', 'UserController@collectVideo');

        // 取消收藏视频
        Route::post('video/cancel', 'UserController@cancelCollectVideo');

        // 发布 / 修改视频
        Route::post('video/release', 'UserController@releaseVideo');

        // 删除视频
        Route::post('video/delete', 'UserController@deleteVideo');

        // 获取用户消息列表
        Route::post('message/list', 'UserController@getMessageList');

        // 阅读消息
        Route::post('message/read', 'UserController@readMessage');

        // 删除消息
        Route::post('message/delete', 'UserController@deleteMessage');

        // 关注用户
        Route::post('follow', 'UserController@followUser');

        // 取消关注用户
        Route::post('unfollow', 'UserController@unfollowUser');

        // 获取文章评论
        Route::post('article/comment', 'UserController@getArticleComment');

        // 后台用户列表页
        Route::post('admin/user/list', 'UserController@getAdminUserList');

        // 禁用 / 启用用户
        Route::post('admin/user/disable', 'UserController@disableUser');

        // 获取文章列表
        Route::post('admin/article/list', 'UserController@getAdminArticleList');

        // 禁用 / 启用文章
        Route::post('admin/article/disable', 'UserController@disableArticle');

        // 获取视频列表
        Route::post('admin/video/list', 'UserController@getAdminVideoList');

        // 禁用 / 启用文章
        Route::post('admin/video/disable', 'UserController@disableVideo');

        // 获取待认证讲师列表
        Route::post('admin/user/certification/list', 'UserController@userCertificationList');

        // 认证讲师
        Route::post('admin/user/certification', 'UserController@userCertification');

        // 获取待审核的视频列表
        Route::post('admin/video/review/list', 'UserController@getVideoReviewList');

        // 审核视频
        Route::post('admin/video/review', 'UserController@reviewVideo');
    });

    // 获取用户基础数据
    Route::post('user/basic', 'UserController@getUserInfoById');

    // 获取用户关注列表
    Route::post('stars', 'UserController@getUserStars');

    // 获取用户粉丝列表
    Route::post('followers', 'UserController@getUserFollowers');

    // 文件上传
    Route::post('upload', 'UserController@uploadFile');

    // 获取文章类型
    Route::post('article/type', 'UserController@getArticleType');

    // 获取文章列表
    Route::post('article/list', 'UserController@getArticleList');

    // 获取文章详情
    Route::post('article/detail', 'UserController@getArticleDetail');

    // 获取视频列表
    Route::post('video/list', 'UserController@getVideoList');

    // 获取视频详情
    Route::post('video/detail', 'UserController@getVideoDetail');

    // 获取推荐用户
    Route::post('user/recommend', 'UserController@getCommendUser');
});
