<?php

namespace App\Http\Controllers\Account;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\User;

use App\Library\Util;

class UserController extends Controller
{
    /**
     * 注册
     *
     * @param Request $request
     * @return void
     */
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
            return Util::responseData(300, $checkParamsResult);
        }

        // 检测两次密码是否一致
        if ($password != $confirm_password) {
            return Util::responseData(201, '两次密码不一致');
        }

        $user = new User;
        $user->account = $request->account;
        $user->password = $request->input('password');
        $user->nickname = $request->input('nickname');
        $user->save();

        return Util::responseData(1, '注册成功');
    }

    public function postLogin(Request $request) {
        $params = ['account', 'password'];
        $checkParamsResult = Util::checkParams($request->all(), $params);

        // 检测必填参数
        if ($checkParamsResult) {
            return Util::responseData(300, $checkParamsResult);
        }

        $account = $request->account;
        $password = $request->password;

        $user = User::where('account', $account)->first();

        // 用户不存在
        if (!$user) {
            return Util::responseData(203, '用户不存在');
        }

        // 密码不一致
        if ($user->password != $password) {
            return Util::responseData(204, '用户名或密码错误');
        }

        $token = Util::generateToken();
        $user->token = $token;
        $user->save();

        return Util::responseData(1, '登录成功', [
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'account' => $user->account,
                'nickname' => $user->nickname,
                'avatar' => $user->avatar,
                'summary' => $user->summary,
                'role_id' => $user->role_id,
                'role_name' => $user->role->role_name,
                'status' => $user->status,
                'created_at' => $user->created_at
            ]
        ]);
    }
}
