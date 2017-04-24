<?php

namespace App\Http\Controllers\Account;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\User;

use App\Library\Util;

class UserController extends Controller
{
    public function postRegister(Request $request) {
        $account = $request->account;

        // 如果该用户已存在
        if ($user = User::where('account', $account)->first()) {
            return Util::responseData(200, '该用户已注册');
        }

        $password = $request->password;
        $confirm_password = $request->confirm_password;
        $nickname = $request->nickname;

        $params = ['account', 'nickname', 'password', 'confirm_password'];
        $checkParamsResult = Util::checkParams($request->all(), $params);

        // 检测参数
        if ($checkParamsResult) {
            return Util::responseData(201, $checkParamsResult);
        }

        // 检测两次密码是否一致
        if ($password != $confirm_password) {
            return Util::responseData(202, '两次密码不一致');
        }

        $user = new User;
        $user->account = $request->account;
        $user->password = $request->input('password');
        $user->nickname = $request->input('nickname');
        $user->save();

        return Util::responseData(1, '注册成功');
    }
}
