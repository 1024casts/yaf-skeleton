<?php

namespace App\Defines;

use PHPCasts\Yaf\Http\Support\Defines\Code as CoreCode;

class Code extends CoreCode
{
    // common
    const PARAMS_ERROR = 200001;
    const SERVER_BUSY = 200002;
    const ILLEGAL_REQUEST = 200003;
    const CAPTCHA_WRONG = 200004;
    const NOT_ALLOW = 200005;
    const AUTH_FAILED = 200006;

    public static $msg = [
        self::PARAMS_ERROR => '参数错误',
        self::SERVER_BUSY => '服务繁忙, 请稍后再试',
        self::NOT_ALLOW => '不允许访问',
        self::AUTH_FAILED => '用户未登录',
    ];
}
