<?php

namespace Core\Exceptions;

/**
 * 应用抛出的异常，认证错误
 */
class AuthException extends AppException
{
    public function __construct($message = 'Auth Failed', $code = 401)
    {
        parent::__construct($message, $code);
    }
}