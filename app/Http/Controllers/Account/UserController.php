<?php

namespace App\Http\Controllers\Account;

use Illuminate\Http\File;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\User;
use App\Models\Follower;
use App\Models\ArticleType;
use App\Models\Collect;
use App\Models\Article;
use App\Models\Video;
use App\Models\Message;

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

        if ($user->role_id === 3) {
            return Util::responseData(205, '您的身份不能登录该平台');
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
                'created_at' => date($user->created_at)
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
            'created_at' => date($user->created_at),

            'authentication' => $user->authentication,
            'status' => $user->status,

            'articles_num' => $user->articles->count(),
            'videos_num' => $user->videos->count(),
            // TODO: 结果有可能不准确，待测试
            'stars_num' => $user->stars->count(),
            'followers_num' => $user->followers->count(),

            'is_follow' => 0
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

        $message = new Message;
        $message->title = '您已提交讲师认证申请，请耐心等待审核';
        $message->user_id = $user->id;
        $message->save();

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
        $token = $request->token;
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

        $login_user = User::where('token', $token)->first();
        $is_follow = null;

        if ($login_user) {
            if ($login_user->id == $user->id) {
                $is_follow = 0;
            }
            else {
                $is_follow = Follower::where('star', $id)->where('follower', $login_user->id)->first() ? 1 : 2;
            }
        }
        else {
            $is_follow = 2;
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
            'created_at' => date($user->created_at),

            'articles_num' => $user->articles->count(),
            'videos_num' => $user->videos->count(),
            // TODO: 结果有可能不准确，待测试
            'stars_num' => $user->stars->count(),
            'followers_num' => $user->followers->count(),

            'is_follow' => $is_follow
        ]);
    }

    /**
     * 修改用户资料
     *
     * @param Request $request
     * @return void
     */
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
            'created_at' => date($user->created_at),

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
     * 获取用户关注列表
     *
     * @param Request $request
     * @return void
     */
    public function getUserStars(Request $request) {
        $params = ['id'];
        $checkParamsResult = Util::checkParams($request->all(), $params);

        // 检测必填参数
        if ($checkParamsResult) {
            return Util::responseData(300, $checkParamsResult);
        }

        $id = $request->id;
        $pageSize = $request->pageSize ? (int) $request->pageSize : 10;
        $user = User::where('id', $id)->first();

        if (!$user) {
            return Util::responseData(200, '没有该用户');
        }

        $followers = $user
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

    /**
     * 获取用户粉丝列表
     *
     * @param Request $request
     * @return void
     */
    public function getUserFollowers(Request $request) {
        $params = ['id'];
        $checkParamsResult = Util::checkParams($request->all(), $params);

        // 检测必填参数
        if ($checkParamsResult) {
            return Util::responseData(300, $checkParamsResult);
        }

        $id = $request->id;
        $pageSize = $request->pageSize ? (int) $request->pageSize : 10;

        $user = User::where('id', $id)->first();

        if (!$user) {
            return Util::responseData(200, '没有该用户');
        }

        $followers = $user
            ->userFollowers()
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

    /**
     * 关注用户
     *
     * @param Request $request
     * @return void
     */
    public function followUser(Request $request) {
        $params = ['id', 'token'];
        $checkParamsResult = Util::checkParams($request->all(), $params);

        // 检测必填参数
        if ($checkParamsResult) {
            return Util::responseData(300, $checkParamsResult);
        }

        $id = $request->id;

        if (!User::where('id', $id)->first()) {
            return Util::responseData(200, '没有该用户');
        }

        $token = $request->token;
        $user = User::where('token', $token)->first();
        $follower_id = $user->id;

        if ($follower_id == $id) {
            return Util::responseData(202, '您不能关注自己');
        }

        if (Follower::where('star', $id)->where('follower', $follower_id)->first()) {
            return Util::responseData(201, '您已关注该用户');
        }

        $follower = new Follower;
        $follower->star = $id;
        $follower->follower = $follower_id;
        $follower->save();

        return Util::responseData(1, '关注成功');
    }

    /**
     * 取消关注用户
     *
     * @param Request $request
     * @return void
     */
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

    /**
     * 上传文件
     *
     * @param Request $request
     * @return void
     */
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

    /**
     * 获取文章类型
     *
     * @param Request $request
     * @return void
     */
    public function getArticleType(Request $request) {
        $articleType = ArticleType::all();
        return Util::responseData(1, '查询成功', [
            'list' => $articleType
        ]);
    }

    /**
     * 获取全部文章列表
     *
     * @param Request $request
     * @return void
     */
    public function getArticleList(Request $request) {
        $id = $request->id;

        $type = $request->type;
        $page = $request->page;
        $article_type = $request->article_type;
        $pageSize = $request->pageSize ? (int) $request->pageSize : 10;

        $articles;

        if ($id) {
            if ($type == 2) {
                $user = User::where('id', $id)->first();

                if (!$user) {
                    return Util::responseData(201, '用户不存在');
                }

                $articles = $user->collectArticles()->where('type', 1)->orderBy('collects.id', 'desc')->paginate($pageSize);
            }
            else {
                $articles = Article::where('author', $id)->where('status', 1)->orderBy('id', 'desc')->paginate($pageSize);
            }
        }
        else {
            if ($article_type) {
                $articles = Article::where('type_id', $article_type)->where('status', 1)->orderBy('id', 'desc')->paginate($pageSize);
            }
            else {
                $articles = Article::where('status', 1)->orderBy('id', 'desc')->paginate($pageSize);
            }
        }

        return Util::responseData(1, '查询成功', [
            'list' => $articles->map(function($item, $key) {
                $article = $item;
                $article['author'] = collect($item->articleAuthor)->only(['id', 'nickname', 'avatar']);
                $article['article_type'] = collect($item->type);
                return collect($article)->forget(['article_author', 'article_type', 'content']);
            }),
            'pagination' => [
                'total' => $articles->total(),
                'current' => $articles->currentPage(),
                'pageSize' => $articles->perPage()
            ]
        ]);
    }

    /**
     * 获取文章详情
     *
     * @param Request $request
     * @return void
     */
    public function getArticleDetail(Request $request) {
        $params = ['id'];
        $checkParamsResult = Util::checkParams($request->all(), $params);

        // 检测必填参数
        if ($checkParamsResult) {
            return Util::responseData(300, $checkParamsResult);
        }

        $id = $request->id;
        $article = Article
            ::with(['articleAuthor' => function($query) {
                $query->select('id', 'nickname', 'avatar')
                    ->withCount('userArticles')
                    ->withCount('userFollowers')
                    ->withCount('userVideos')
                    ->get();
            }])
            ->withCount('collects')
            ->find($id);

        if (!$article) {
            return Util::responseData(200, '没有该文章');
        }

        if ($article->status == 2) {
            return Util::responseData(201, '该文章已禁用');
        }

        if ($article->status == 3) {
            return Util::responseData(202, '该文章已删除');
        }

        $user = User::where('token', $request->token)->first();
        $isCollect = 2;

        if ($user) {
            if ($user->id === $article->author) {
                $isCollect = 0;
            }

            if ($user->collectArticles()->find($id)) {
                $isCollect = 1;
            }
        }

        $article->read_num = $article->read_num + 1;
        $article->save();

        return Util::responseData(1, '查询成功', collect($article)->put('is_collect', $isCollect));
    }

    /**
     * 收藏文章
     *
     * @param Request $request
     * @return void
     */
    public function collectArticle(Request $request) {
        $params = ['id', 'token'];
        $checkParamsResult = Util::checkParams($request->all(), $params);

        // 检测必填参数
        if ($checkParamsResult) {
            return Util::responseData(300, $checkParamsResult);
        }

        $id = $request->id;
        $token = $request->token;

        $user = User::where('token', $token)->first();
        $article = Article::find($id);

        if (!$article) {
            return Util::responseData(200, '没有该文章');
        }

        if ($article->author == $user->id) {
            return Util::responseData(201, '您不能收藏自己的文章');
        }

        if (Collect::where('article_id', $id)->where('user_id', $user->id)->first()) {
            return Util::responseData(202, '您已收藏该文章');
        }

        $collect = new Collect;
        $collect->user_id = $user->id;
        $collect->article_id = $id;
        $collect->type = 1;
        $collect->save();

        return Util::responseData(1, '文章收藏成功');
    }

    /**
     * 取消收藏文章
     *
     * @param Request $request
     * @return void
     */
    public function cancelCollectArticle(Request $request) {
        $params = ['id', 'token'];
        $checkParamsResult = Util::checkParams($request->all(), $params);

        // 检测必填参数
        if ($checkParamsResult) {
            return Util::responseData(300, $checkParamsResult);
        }

        $id = $request->id;
        $token = $request->token;

        $collect = Collect::where('article_id', $id)->first();

        if (!$collect) {
            return Util::responseData(200, '您没有收藏该文章');
        }

        $collect->delete();

        return Util::responseData(1, '文章取消收藏成功');
    }

    /**
     * 发布 / 更新文章
     *
     * @param Request $request
     * @return void
     */
    public function releaseArticle(Request $request) {
        $params = ['token', 'title', 'cover', 'summary', 'content', 'type_id'];
        $checkParamsResult = Util::checkParams($request->all(), $params);

        // 检测必填参数
        if ($checkParamsResult) {
            return Util::responseData(300, $checkParamsResult);
        }

        $id = $request->id;
        $article;
        $errmsg;
        $success;

        $user = User::where('token', $request->token)->first();

        if ($id) {
            $article = Article::find($id);
            if (!$article) {
                return Util::responseData(200, '文章不存在');
            }
            if ($user->id != $article->author) {
                return Util::responseData(201, '您不能修改别人发布的文章');
            }
            $errmsg = '修改失败';
            $success = '修改成功';
        }
        else {
            $errmsg = '添加失败';
            $success = '添加成功';
            $article = new Article;
            $article->author = $user->id;
        }

        $article->title = $request->title;
        $article->cover = $request->cover;
        $article->summary = $request->summary;
        $article->content = $request->content;
        $article->type_id = $request->type_id;
        $result = $article->save();

        if ($result) {
            return Util::responseData(1, $success);
        }
        else {
            return Util::responseData(0, $errmsg);
        }
    }

    public function deleteArticle(Request $request) {
        $params = ['token', 'id'];
        $checkParamsResult = Util::checkParams($request->all(), $params);

        // 检测必填参数
        if ($checkParamsResult) {
            return Util::responseData(300, $checkParamsResult);
        }

        $article = Article::find($request->id);

        if (!$article) {
            return Util::responseData(200, '文章不存在');
        }

        $user = User::where('token', $request->token)->first();

        if ($article->author != $user->id) {
            return Util::responseData(201, '您没有权限删除别人的文章');
        }

        $article->status = 3;
        $article->save();
        $article->delete();

        return Util::responseData(1, '删除成功');
    }

    /**
     * 获取视频列表
     *
     * @param Request $request
     * @return void
     */
    public function getVideoList(Request $request) {
        $id = $request->id;
        $type = $request->type;
        $page = $request->page;
        $pageSize = $request->pageSize ? (int) $request->pageSize : 10;

        $videos;

        if ($id) {
            if ($type == 2) {
                $user = User::where('id', $id)->first();

                if (!$user) {
                    return Util::responseData(201, '用户不存在');
                }

                $videos = $user->collectVideos()->where('type', 2)->orderBy('collects.id', 'desc')->paginate($pageSize);
            }
            else {
                $videos = Video::where('author', $id)->where('status', 1)->orderBy('id', 'desc')->paginate($pageSize);
            }
        }
        else {
            $videos = Video::where('status', 1)->orderBy('id', 'desc')->paginate($pageSize);
        }

        return Util::responseData(1, '查询成功', [
            'list' => $videos->map(function($item, $key) {
                $video = $item;
                $video['author'] = collect($item->videoAuthor)->only(['id', 'nickname', 'avatar']);
                return collect($video)->forget(['video_author', 'video_url']);
            }),
            'pagination' => [
                'total' => $videos->total(),
                'current' => $videos->currentPage(),
                'pageSize' => $videos->perPage()
            ]
        ]);
    }

    /**
     * 获取视频详情
     *
     * @param Request $request
     * @return void
     */
    public function getVideoDetail(Request $request) {
        $params = ['id'];
        $checkParamsResult = Util::checkParams($request->all(), $params);

        // 检测必填参数
        if ($checkParamsResult) {
            return Util::responseData(300, $checkParamsResult);
        }

        $id = $request->id;
        $video = Video
            ::with(['videoAuthor' => function($query) {
                $query->select('id', 'nickname', 'avatar')
                    ->withCount('userArticles')
                    ->withCount('userFollowers')
                    ->withCount('userVideos')
                    ->get();
            }])
            ->withCount('collects')
            ->find($id);

        if (!$video) {
            return Util::responseData(200, '没有该视频');
        }

        if ($video->status == 2) {
            return Util::responseData(201, '该视频已禁用');
        }

        if ($video->status == 3) {
            return Util::responseData(202, '该视频已删除');
        }

        $user = User::where('token', $request->token)->first();
        $isCollect = 2;

        if ($user) {
            if ($user->id === $video->author) {
                $isCollect = 0;
            }

            if ($user->collectVideos()->find($id)) {
                $isCollect = 1;
            }
        }

        $video->play_num = $video->play_num + 1;
        $video->save();

        return Util::responseData(1, '查询成功', collect($video)->put('is_collect', $isCollect));
    }

    /**
     * 收藏视频
     *
     * @param Request $request
     * @return void
     */
    public function collectVideo(Request $request) {
        $params = ['id', 'token'];
        $checkParamsResult = Util::checkParams($request->all(), $params);

        // 检测必填参数
        if ($checkParamsResult) {
            return Util::responseData(300, $checkParamsResult);
        }

        $id = $request->id;
        $token = $request->token;

        $user = User::where('token', $token)->first();
        $video = Video::find($id);

        if (!$video) {
            return Util::responseData(200, '没有该文章');
        }

        if ($video->author == $user->id) {
            return Util::responseData(201, '您不能收藏自己的视频');
        }

        if (Collect::where('video_id', $id)->where('user_id', $user->id)->first()) {
            return Util::responseData(202, '您已收藏该视频');
        }

        $collect = new Collect;
        $collect->user_id = $user->id;
        $collect->video_id = $id;
        $collect->type = 2;
        $collect->save();

        return Util::responseData(1, '视频收藏成功');
    }

    /**
     * 取消收藏视频
     *
     * @param Request $request
     * @return void
     */
    public function cancelCollectVideo(Request $request) {
        $params = ['id', 'token'];
        $checkParamsResult = Util::checkParams($request->all(), $params);

        // 检测必填参数
        if ($checkParamsResult) {
            return Util::responseData(300, $checkParamsResult);
        }

        $id = $request->id;
        $token = $request->token;

        $collect = Collect::where('video_id', $id)->first();

        if (!$collect) {
            return Util::responseData(200, '您没有收藏该视频');
        }

        $collect->delete();

        return Util::responseData(1, '视频取消收藏成功');
    }

    /**
     * 发布 / 修改视频
     *
     * @param Request $request
     * @return void
     */
    public function releaseVideo (Request $request) {
        $params = ['token', 'title', 'cover', 'summary', 'video_url'];
        $checkParamsResult = Util::checkParams($request->all(), $params);

        // 检测必填参数
        if ($checkParamsResult) {
            return Util::responseData(300, $checkParamsResult);
        }

        $id = $request->id;
        $video;
        $errmsg;
        $success;

        $user = User::where('token', $request->token)->first();

        if ($id) {
            $video = Video::find($id);
            if (!$video) {
                return Util::responseData(200, '文章不存在');
            }
            if ($user->id != $video->author) {
                return Util::responseData(201, '您不能修改别人发布的文章');
            }
            $errmsg = '修改失败';
            $success = '修改成功';
        }
        else {
            $errmsg = '添加失败';
            $success = '添加成功';
            $video = new Video;
            $video->author = $user->id;
        }

        $video->title = $request->title;
        $video->cover = $request->cover;
        $video->summary = $request->summary;
        $video->video_url = $request->video_url;
        $result = $video->save();

        if ($result) {
            return Util::responseData(1, $success);
        }
        else {
            return Util::responseData(0, $errmsg);
        }
    }

    public function deleteVideo(Request $request) {
        $params = ['token', 'id'];
        $checkParamsResult = Util::checkParams($request->all(), $params);

        // 检测必填参数
        if ($checkParamsResult) {
            return Util::responseData(300, $checkParamsResult);
        }

        $video = Video::find($request->id);

        if (!$video) {
            return Util::responseData(200, '视频不存在');
        }

        $user = User::where('token', $request->token)->first();

        if ($video->author != $user->id) {
            return Util::responseData(201, '您没有权限删除别人的视频');
        }

        $video->status = 3;
        $video->save();
        $video->delete();

        return Util::responseData(1, '删除成功');
    }

    public function getMessageList(Request $request) {
        $params = ['token'];
        $checkParamsResult = Util::checkParams($request->all(), $params);

        // 检测必填参数
        if ($checkParamsResult) {
            return Util::responseData(300, $checkParamsResult);
        }

        $pageSize = $request->pageSize ? (int) $request->pageSize : 10;
        $user = User::where('token', $request->token)->first();
        $messages = $user->messages()->orderBy('id', 'desc')->paginate();

        return Util::responseData(1, '查询成功', [
            'list' => $messages->all(),
            'pagination' => [
                'total' => $messages->total(),
                'current' => $messages->currentPage(),
                'pageSize' => $messages->perPage()
            ]
        ]);
    }

    /**
     * 阅读消息
     *
     * @param Request $request
     * @return void
     */
    public function readMessage(Request $request) {
        $params = ['token', 'id'];
        $checkParamsResult = Util::checkParams($request->all(), $params);

        // 检测必填参数
        if ($checkParamsResult) {
            return Util::responseData(300, $checkParamsResult);
        }

        $user = User::where('token', $request->token)->first();
        $message = $user->messages()->find($request->id);

        if (!$message) {
            return Util::responseData(200, '没有该消息');
        }

        $message->status = 2;
        $message->save();

        return Util::responseData(1, '已读');
    }

    /**
     * 删除消息
     *
     * @param Request $request
     * @return void
     */
    public function deleteMessage(Request $request) {
        $params = ['token', 'id'];
        $checkParamsResult = Util::checkParams($request->all(), $params);

        // 检测必填参数
        if ($checkParamsResult) {
            return Util::responseData(300, $checkParamsResult);
        }

        $user = User::where('token', $request->token)->first();
        $message = $user->messages()->find($request->id);

        if (!$message) {
            return Util::responseData(200, '没有该消息');
        }

        $message->delete();

        return Util::responseData(1, '删除成功');
    }

    public function getArticleComment(Request $request) {
        $params = ['token', 'id'];
        $checkParamsResult = Util::checkParams($request->all(), $params);

        // 检测必填参数
        if ($checkParamsResult) {
            return Util::responseData(300, $checkParamsResult);
        }

        $id = $request->id;
        $token = $request->token;

        $article = Article::find($id);

        if (!$article) {
            return Util::responseData(200, '没有该文章');
        }

        $user = User::where('token', $token)->first();

        return Util::responseData(1, '查询成功', $article->comments);
    }

    /**
     * 管理员登录
     *
     * @param Request $request
     * @return void
     */
    public function adminLogin(Request $request) {
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

        if ($user->role_id != 3) {
            return Util::responseData(205, '只有管理员才可以登录');
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
                'created_at' => date($user->created_at)
            ]
        ]);
    }

    /**
     * 获取用户列表
     *
     * @param Request $request
     * @return void
     */
    public function getAdminUserList(Request $request) {
        $pageSize = $request->pageSize ? (int) $request->pageSize : 6;
        $filter = $request->filter;
        $user = null;

        if ($filter) {
            $users = User::where('status', '<>', 3)
            ->where(function($query) use ($filter) {
                $query->where('account', 'like', '%'.$filter.'%')
                    ->orWhere('nickname', 'like', '%'.$filter.'%')
                    ->orWhere('id', $filter);
            })
            ->with('role')->paginate($pageSize);
        }
        else {
            $users = User::where('status', '<>', 3)->with('role')->paginate($pageSize);
        }

        return Util::responseData(1, '查询成功', [
            'list' => $users->all(),
            'pagination' => [
                'total' => $users->total(),
                'current' => $users->currentPage(),
                'pageSize' => $users->perPage()
            ]
        ]);
    }

    /**
     * 禁用 / 启用用户
     *
     * @param Request $request
     * @return void
     */
    public function disableUser(Request $request) {
        $params = ['id', 'disable'];
        $checkParamsResult = Util::checkParams($request->all(), $params);

        // 检测必填参数
        if ($checkParamsResult) {
            return Util::responseData(300, $checkParamsResult);
        }

        $option = '';
        $user = User::find($request->id);

        if (!$user) {
            return Util::responseData(200, '没有该用户');
        }

        $option = $request->disable == 1 ? '启用' : '禁用';

        if ($user->role_id == 3) {
            return Util::responseData(201, '您没有权限'.$option.'该用户');
        }

        $admin = User::where('token', $request->token)->first();
        $user->status = $request->disable;

        if ($user->save()) {
            return Util::responseData(1, $option.'成功');
        }
        else {
            return Util::responseData(0, $option.'失败');
        }
    }

    /**
     * 获取文章列表
     *
     * @param Request $request
     * @return void
     */
    public function getAdminArticleList(Request $request) {
        $pageSize = $request->pageSize ? (int) $request->pageSize : 6;
        $filter = $request->filter;
        $articles = null;

        if ($filter) {
            $articles = Article::where('status', '<>', 3)
                ->where(function($query) use ($filter) {
                    $query->where('title', 'like', '%'.$filter.'%')
                        ->orWhere('content', 'like', '%'.$filter.'%')
                        ->orWhere('summary', 'like', '%'.$filter.'%')
                        ->orWhere('id', $filter);
                })
                ->with(['articleAuthor' => function($query) {
                    $query->select('id', 'nickname');
                }, 'type'])
                ->paginate($pageSize);
        }
        else {
            $articles = Article::where('status', '<>', 3)
                ->with(['articleAuthor' => function($query) {
                    $query->select('id', 'nickname');
                }, 'type'])
                ->paginate($pageSize);
        }

        return Util::responseData(1, '查询成功', [
            'list' => $articles->all(),
            'pagination' => [
                'total' => $articles->total(),
                'current' => $articles->currentPage(),
                'pageSize' => $articles->perPage()
            ]
        ]);
    }

    /**
     * 启用 / 禁用文章
     *
     * @param Request $request
     * @return void
     */
    public function disableArticle(Request $request) {
        $params = ['id', 'disable'];
        $checkParamsResult = Util::checkParams($request->all(), $params);

        // 检测必填参数
        if ($checkParamsResult) {
            return Util::responseData(300, $checkParamsResult);
        }

        $option = '';
        $article = Article::find($request->id);

        if (!$article) {
            return Util::responseData(200, '该文章不存在');
        }

        $option = $request->disable == 1 ? '启用' : '禁用';
        $admin = User::where('token', $request->token)->first();
        $article->status = $request->disable;

        if ($article->save()) {
            return Util::responseData(1, $option.'成功');
        }
        else {
            return Util::responseData(0, $option.'失败');
        }
    }

    /**
     * 获取视频列表
     *
     * @param Request $request
     * @return void
     */
    public function getAdminVideoList(Request $request) {
        $pageSize = $request->pageSize ? (int) $request->pageSize : 6;
        $filter = $request->filter;
        $videos = null;

        if ($filter) {
            $videos = Video::where('status', '<>', 3)
                ->where(function($query) use ($filter) {
                    $query->where('title', 'like', '%'.$filter.'%')
                        ->orWhere('summary', 'like', '%'.$filter.'%')
                        ->orWhere('id', $filter);
                })
                ->with(['videoAuthor' => function($query) {
                    $query->select('id', 'nickname');
                }])
                ->paginate($pageSize);
        }
        else {
            $videos = Video::where('status', '<>', 3)
                ->with(['videoAuthor' => function($query) {
                    $query->select('id', 'nickname');
                }])
                ->paginate($pageSize);
        }

        return Util::responseData(1, '查询成功', [
            'list' => $videos->all(),
            'pagination' => [
                'total' => $videos->total(),
                'current' => $videos->currentPage(),
                'pageSize' => $videos->perPage()
            ]
        ]);
    }

    /**
     * 禁用 / 启用视频
     *
     * @param Request $request
     * @return void
     */
    public function disableVideo(Request $request) {
        $params = ['id', 'disable'];
        $checkParamsResult = Util::checkParams($request->all(), $params);

        // 检测必填参数
        if ($checkParamsResult) {
            return Util::responseData(300, $checkParamsResult);
        }

        $option = '';
        $video = Video::find($request->id);

        if (!$video) {
            return Util::responseData(200, '没有该视频');
        }

        $option = $request->disable == 1 ? '启用' : '禁用';
        $admin = User::where('token', $request->token)->first();
        $video->status = $request->disable;

        if ($video->save()) {
            return Util::responseData(1, $option.'成功');
        }
        else {
            return Util::responseData(0, $option.'失败');
        }
    }

    public function userCertificationList(Request $request) {
        $pageSize = $request->pageSize ? (int) $request->pageSize : 6;
        $filter = $request->filter;
        $users = null;

        if ($filter) {
            $users = User::where('authentication', 2)
                ->where(function($query) use ($filter) {
                    $query->where('account', 'like', '%'.$filter.'%')
                        ->orWhere('nickname', 'like', '%'.$filter.'%')
                        ->orWhere('id', $filter);
                })
                ->where('status', 1)
                ->where('role_id', 1)
                ->paginate($pageSize);
        }
        else {
            $users = User::where('authentication', 2)
                ->where('status', 1)
                ->where('role_id', 1)
                ->paginate($pageSize);
        }

        return Util::responseData(1, '查询成功', [
            'list' => $users->all(),
            'pagination' => [
                'total' => $users->total(),
                'current' => $users->currentPage(),
                'pageSize' => $users->perPage()
            ]
        ]);
    }

    /**
     * 审核用户
     *
     * @param Request $request
     * @return void
     */
    public function userCertification(Request $request) {
        $params = ['id', 'result'];
        $checkParamsResult = Util::checkParams($request->all(), $params);

        // 检测必填参数
        if ($checkParamsResult) {
            return Util::responseData(300, $checkParamsResult);
        }

        $id = $request->id;
        $user = User::find($id);

        if (!$user) {
            return Util::responseData(200, '该用户不存在');
        }

        if ($user->status == 2) {
            return Util::responseData(201, '该用户已禁用');
        }

        if ($user->authentication != 2) {
            return Util::responseData(202, '该用户没有申请认证');
        }

        $result = $request->result;
        $user->authentication = $result;

        if ($result == 4) {
            $user->role_id = 2;
        }

        if ($user->save()) {
            $message = new Message;
            $message->user_id = $id;
            $message->title = $result == 3 ? '您没有通过讲师认证' : '恭喜您已成功通过讲师认证';
            $message->content = $request->content;
            $message->save();
            return Util::responseData(1, '操作成功');
        }
        else {
            return Util::responseData(0, '操作失败');
        }
    }

    public function getVideoReviewList(Request $request) {
        $pageSize = $request->pageSize ? (int) $request->pageSize : 6;
        $filter = $request->filter;
        $videos = null;

        if ($filter) {
            $videos = Video::where('status', 4)
                ->where(function($query) use ($filter) {
                    $query->where('title', 'like', '%'.$filter.'%')
                        ->orWhere('summary', 'like', '%'.$filter.'%')
                        ->orWhere('id', $filter);
                })
                ->with(['videoAuthor' => function($query) {
                    $query->select('id', 'nickname');
                }])
                ->paginate($pageSize);
        }
        else {
            $videos = Video::where('status', 4)
                ->with(['videoAuthor' => function($query) {
                    $query->select('id', 'nickname');
                }])
                ->paginate($pageSize);
        }

        return Util::responseData(1, '查询成功', [
            'list' => $videos->all(),
            'pagination' => [
                'total' => $videos->total(),
                'current' => $videos->currentPage(),
                'pageSize' => $videos->perPage()
            ]
        ]);
    }

    public function reviewVideo(Request $request) {
        $params = ['id', 'result'];
        $checkParamsResult = Util::checkParams($request->all(), $params);

        // 检测必填参数
        if ($checkParamsResult) {
            return Util::responseData(300, $checkParamsResult);
        }

        $id = $request->id;
        $video = Video::find($id);

        if (!$video) {
            return Util::responseData(200, '该视频不存在');
        }

        if ($video->status != 4) {
            return Util::responseData(201, '该视频不在待审核状态');
        }

        $result = $request->result;
        $video->status = $result;

        if ($video->save()) {
            $message = new Message;
            $message->user_id = $video->author;
            $message->title = $result == 5 ? '您的视频未通过审核' : '您的视频已通过审核';
            $message->content = $request->content;
            $message->save();
            return Util::responseData(1, '操作成功');
        }
        else {
            return Util::responseData(0, '操作失败');
        }
    }
}
