<?php

namespace Core\Http\Support\Defines;

/**
 * 状态码定义
 */
class Code
{
    const UNDEFINED_ERROR = 0; // 未定义错误
    const SUCCESS = 200; // 成功
    const BAD_REQUEST = 400; // 请求错误
    const AUTH_FAILED = 401; // 验证失败
    const PERMISSION_DENIED = 403; // 无权限
    const NOT_FOUND = 404; // 资源不存在
    const PARAMS_ERROR = 406; // 请求参数错误
    const NOT_LOGIN = 411; // 未登录
    const SERVER_ERROR = 500; // 系统错误

    /**
     * 状态码对应的描述,子类可直接继承覆盖
     *
     * @var array
     */
    public static $msg = [
        self::UNDEFINED_ERROR => 'undefined error',
        self::SUCCESS => 'ok',
        self::BAD_REQUEST => 'bad request',
        self::AUTH_FAILED => 'auth failed',
        self::PERMISSION_DENIED => 'permission denied',
        self::NOT_FOUND => 'not found',
        self::PARAMS_ERROR => 'params error',
        self::NOT_LOGIN => 'not login',
        self::SERVER_ERROR => 'server error',
    ];

    /**
     * 通过$code获取对应的错误信息
     *
     * @param int $code
     * @return string
     */
    public static function getMsg($code)
    {
        return isset(static::$msg[$code]) ? static::$msg[$code] : static::$msg[static::UNDEFINED_ERROR];
    }
}