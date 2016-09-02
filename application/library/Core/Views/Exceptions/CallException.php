<?php

namespace Core\Views\Exceptions;

use Core\Exceptions\AppException;

/**
 * 调用错误
 */
class CallException extends AppException
{
    /**
     * CallException constructor.
     *
     * @param string $name 名称
     * @param int $code 错误码
     */
    public function __construct($name, $code = 0)
    {
        parent::__construct("Method '{$name}' is undefined.", $code);
    }
}