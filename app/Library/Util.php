<?php

namespace App\Library;

class Util
{
    // 生成返回数据
    static function responseData($code, $message, $data = null) {
        return response()->json([
            'errcode' => $code,
            'errmsg' => $message,
            'data' => $data
        ]);
    }

    // 判断参数
    static function checkParams($data, $items) {
        $message = '';

        foreach ($items as $item) {
            if (!key_exists($item, $data)) {
                $message = $message.$item.',';
            }
        }

        if ($message == '') {
            return false;
        }

        return '缺少参数：'.substr($message, 0, -1);
    }

    // 生成 token
    static function generateToken() {
        return md5(rand(10000, 100000000));
    }
}
