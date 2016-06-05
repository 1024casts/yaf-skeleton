<?php

namespace Core\Exceptions;

/**
 * 应用抛出的异常，参数错误
 */
class ParamsException extends AppException
{
    public function __construct($paramName, $message, $code = 400)
    {
        parent::__construct('Error param: $' . $paramName . ' ' . $message, $code);
    }
}