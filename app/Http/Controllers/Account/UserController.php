<?php

namespace App\Http\Controllers\Account;

use Illuminate\Http\File;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\User;
use App\Models\Follower;
use App\Models\ArticleType;

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

    /**
     * 登录
     *
     * @param Request $request
     * @return void
     */
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

    /**
     * 重新设置密码
     *
     * @param Request $request
     * @return void
     */
    public function postReset(Request $request) {
        $params = ['password', 'new_password', 'confirm_password'];
        $checkParamsResult = Util::checkParams($request->all(), $params);

        // 检测必填参数
        if ($checkParamsResult) {
            return Util::responseData(300, $checkParamsResult);
        }

        $token = $request->token;
        $password = $request->password;
        $new_password = $request->new_password;
        $confirm_password = $request->confirm_password;

        if ($new_password != $confirm_password) {
            return Util::responseData(200, '两次密码不一致');
        }

        $user = User::where('token', $request->token)->first();

        if ($password != $user->password) {
            return Util::responseData(201, '原密码不正确');
        }

        $user->password = $new_password;
        $user->save();

        return Util::responseData(1, '密码修改成功');
    }

    /**
     * 通过 token 获取个人信息
     *
     * @param Request $request
     * @return void
     */
    public function getUserInfo(Request $request) {
        $token = $request->token;
        $user = User::where('token', $token)->first();

        if (!$user) {
            return Util::responseData(0, '获取用户数据失败');
        }

        return Util::responseData(1, '获取用户数据成功', [
            'id' => $user->id,
            'account' => $user->account,
            'nickname' => $user->nickname,
            'avatar' => $user->avatar,
            'summary' => $user->summary,
            'role_id' => $user->role_id,
            'role_name' => $user->role->role_name,
            'card_number' => $user->card_number,
            'card_front_image' => $user->card_front_image,
            'card_back_image' => $user->card_back_image,
            'created_at' => $user->created_at,

            'authentication' => $user->authentication,
            'status' => $user->status,

            'articles_num' => $user->articles->count(),
            'videos_num' => $user->videos->count(),
            // TODO: 结果有可能不准确，待测试
            'stars_num' => $user->stars->count(),
            'followers_num' => $user->stars->count()
        ]);
    }

    /**
     * 申请成为讲师
     *
     * @param Request $request
     * @return void
     */
    public function applyLecturer(Request $request) {
        $params = ['card_number', 'card_front_image', 'card_back_image'];
        $checkParamsResult = Util::checkParams($request->all(), $params);

        // 检测必填参数
        if ($checkParamsResult) {
            return Util::responseData(300, $checkParamsResult);
        }

        $token = $request->token;
        $card_number = $request->card_number;
        $card_front_image = $request->card_front_image;
        $card_back_image = $request->card_back_image;

        $user = User::where('token', $token)->first();

        if ($user->role_id == 2) {
            return Util::responseData(200, '您已经是讲师身份，无需再次申请');
        }

        if ($user->authentication == 2) {
            return Util::responseData(201, '正在审核您的资料，请耐心等待');
        }

        $user->card_back_image = $card_back_image;
        $user->card_front_image = $card_front_image;
        $user->card_back_image = $card_back_image;
        $user->authentication = 2;
        $user->save();

        return Util::responseData(1, '申请成功，请耐心等待审核通过');
    }

    /**
     * 通过 id 获取用户基本信息
     *
     * @param Request $request
     * @return void
     */
    public function getUserInfoById(Request $request) {
        $params = ['id'];
        $checkParamsResult = Util::checkParams($request->all(), $params);

        // 检测必填参数
        if ($checkParamsResult) {
            return Util::responseData(300, $checkParamsResult);
        }

        $id = $request->id;
        $user = User::where('id', $id)->first();

        if (!$user) {
            return Util::responseData(200, '该用户不存在');
        }

        if ($user->status == 2) {
            return Util::responseData(201, '该用户已禁用');
        }

        if ($user->status == 3) {
            return Util::responseData(202, '该账户已删除');
        }

        return Util::responseData(1, '查询成功', [
            'id' => $user->id,
            'account' => $user->account,
            'nickname' => $user->nickname,
            'avatar' => $user->avatar,
            'summary' => $user->summary,
            'role_id' => $user->role_id,
            'role_name' => $user->role->role_name,
            'status' => $user->status,
            'created_at' => $user->created_at,

            'articles_num' => $user->articles->count(),
            'videos_num' => $user->videos->count(),
            // TODO: 结果有可能不准确，待测试
            'stars_num' => $user->stars->count(),
            'followers_num' => $user->stars->count()
        ]);
    }

    public function updateUserInfo(Request $request) {
        $token = $request->token;
        $avatar = $request->avatar;
        $summary = $request->summary;
        $nickname = $request->nickname;

        $user = User::where('token', $token)->first();

        if ($avatar) {
            $user->avatar = $avatar;
        }

        if ($summary) {
            $user->summary = $summary;
        }

        if ($nickname) {
            $user->nickname = $nickname;
        }

        $user->save();

        return Util::responseData(1, '修改成功', [
            'id' => $user->id,
            'account' => $user->account,
            'nickname' => $user->nickname,
            'avatar' => $user->avatar,
            'summary' => $user->summary,
            'role_id' => $user->role_id,
            'role_name' => $user->role->role_name,
            'card_number' => $user->card_number,
            'card_front_image' => $user->card_front_image,
            'card_back_image' => $user->card_back_image,
            'created_at' => $user->created_at,

            'authentication' => $user->authentication,
            'status' => $user->status,

            'articles_num' => $user->articles->count(),
            'videos_num' => $user->videos->count(),
            // TODO: 结果有可能不准确，待测试
            'stars_num' => $user->stars->count(),
            'followers_num' => $user->stars->count()
        ]);
    }

    public function getUserStars(Request $request) {
        $params = ['id'];
        $checkParamsResult = Util::checkParams($request->all(), $params);

        // 检测必填参数
        if ($checkParamsResult) {
            return Util::responseData(300, $checkParamsResult);
        }

        $id = $request->id;
        $pageSize = $request->pageSize ? (int) $request->pageSize : 10;

        $followers = User::where('id', $id)
            ->first()
            ->userStars()
            ->paginate($pageSize, ['users.id', 'nickname', 'avatar', 'summary']);

        return Util::responseData(1, '查询成功', [
            'list' => $followers->all(),
            'pagination' => [
                'total' => $followers->total(),
                'current' => $followers->currentPage(),
                'pageSize' => $followers->perPage()
            ]
        ]);
    }

    public function getUserFollowers(Request $request) {
        $params = ['id'];
        $checkParamsResult = Util::checkParams($request->all(), $params);

        // 检测必填参数
        if ($checkParamsResult) {
            return Util::responseData(300, $checkParamsResult);
        }

        $id = $request->id;
        $pageSize = $request->pageSize ? (int) $request->pageSize : 10;

        $followers = User::where('id', $id)
            ->first()
            ->userStars()
            ->paginate($pageSize, ['users.id', 'nickname', 'avatar', 'summary']);

        return Util::responseData(1, '查询成功', [
            'list' => $followers->all(),
            'pagination' => [
                'total' => $followers->total(),
                'current' => $followers->currentPage(),
                'pageSize' => $followers->perPage()
            ]
        ]);
    }

    public function followUser(Request $request) {
        $params = ['id', 'token'];
        $checkParamsResult = Util::checkParams($request->all(), $params);

        // 检测必填参数
        if ($checkParamsResult) {
            return Util::responseData(300, $checkParamsResult);
        }

        $id = $request->id;

        if (!User::where('id', $id)->first()) {
            Util::responseData(200, '没有该用户');
        }

        $token = $request->token;
        $user = User::where('token', $token)->first();
        $follower_id = $user->id;

        if (Follower::where('star', $id)->where('follower', $follower_id)->first()) {
            return Util::responseData(201, '您已关注该用户');
        }

        $follower = new Follower;
        $follower->star = $id;
        $follower->follower = $follower_id;
        $follower->save();

        return Util::responseData(1, '关注成功');
    }

    public function unfollowUser(Request $request) {
        $params = ['id', 'token'];
        $checkParamsResult = Util::checkParams($request->all(), $params);

        // 检测必填参数
        if ($checkParamsResult) {
            return Util::responseData(300, $checkParamsResult);
        }

        $id = $request->id;

        if (!User::where('id', $id)->first()) {
            Util::responseData(200, '没有该用户');
        }

        $token = $request->token;
        $user = User::where('token', $token)->first();
        $follower_id = $user->id;
        $follower = Follower::where('star', $id)->where('follower', $follower_id)->first();

        if (!$follower) {
            return Util::responseData(201, '您没有关注该用户');
        }

        $follower->forceDelete();
        return Util::responseData(1, '取消关注成功');
    }

    public function uploadFile(Request $request) {
        $params = ['file'];
        $checkParamsResult = Util::checkParams($request->all(), $params);

        // 检测必填参数
        if ($checkParamsResult) {
            return Util::responseData(300, $checkParamsResult);
        }

        $path = $request->file('file')->store('/uploads');

        return Util::responseData(1, '上传成功', [
            'file_path' => '/'.$path
        ]);
    }

    public function getArticleType(Request $request) {
        $articleType = ArticleType::all();
        return Util::responseData(1, '查询成功', [
            'list' => $articleType
        ]);
    }
}
