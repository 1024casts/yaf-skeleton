<?php

namespace Core\Exceptions;

/**
 * 应用抛出的异常，资源未找到
 */
class NotFoundException extends AppException
{
    public function __construct($message = 'Not Found')
    {
        parent::__construct($message, 404);
    }
}