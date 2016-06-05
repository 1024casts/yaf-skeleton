<?php

namespace Core\Http;

use Yaf\Response_Abstract;

class Response extends Response_Abstract
{
    use ResponseTrait;

    const HTTP_UNDEFINED_ERROR = 0;         // 未定义错误
    const HTTP_SUCCESS = 200;               // 成功
    const HTTP_BAD_REQUEST = 400;           // 请求错误
    const HTTP_UNAUTHORIZED = 401;          // 验证失败
    const HTTP_FORBIDDEN = 403;             // 无权限
    const HTTP_NOT_FOUND = 404;             // 验证失败
    const HTTP_INTERNAL_SERVER_ERROR = 500; // 系统错误

    /**
     * 状态码对应的描述
     *
     * @var array
     */
    public static $msg = [
        self::HTTP_UNDEFINED_ERROR => 'Undefined Error',
        self::HTTP_SUCCESS => 'Ok',
        self::HTTP_BAD_REQUEST => 'Bad Request',
        self::HTTP_UNAUTHORIZED => 'Unauthorized',
        self::HTTP_FORBIDDEN => 'Forbidden',
        self::HTTP_NOT_FOUND => 'Not Found',
        self::HTTP_INTERNAL_SERVER_ERROR => 'Internal Server Error',
    ];

}