<?php

namespace Core\Controllers;


use Core\Exceptions\RuntimeException;

/**
 * Console相关的基础控制器
 */
class Console extends Base
{
    /**
     * 获取视图
     *
     * @throws RuntimeException
     */
    public function getView()
    {
        throw new RuntimeException('Abandon method!');
    }

    /**
     * 渲染模板并输出
     *
     * @param string $actionName
     * @param array $varArray
     * @throws RuntimeException
     * @return bool
     */
    public function display($actionName, array $varArray = [])
    {
        throw new RuntimeException('Abandon method!');
    }
}