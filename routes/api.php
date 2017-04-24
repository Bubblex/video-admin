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

// Route::middleware('auth:api')->get('/api/user', function (Request $request) {
//     return $request->user();
// });

Route::group(['prefix' => 'account', 'namespace' => 'Account'], function() {
    // 注册接口
    Route::post('register', 'UserController@postRegister');
    Route::post('login', 'UserController@postLogin');
});

Route::get('login', function() {
    return response()->json([
        'errcode' => 1
    ]);
});
