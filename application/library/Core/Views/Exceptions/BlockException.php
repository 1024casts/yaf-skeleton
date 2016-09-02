<?php

namespace Core\Views\Exceptions;

use Core\Exceptions\AppException;

/**
 * 块错误
 */
class BlockException extends AppException
{
    /**
     * BlockException constructor.
     *
     * @param string $name 块名称
     * @param int $error 错误内容
     * @param int $code 错误码
     */
    public function __construct($name, $error, $code = 0)
    {
        parent::__construct("Block '{$name}' {$error}.", $code);
    }
}