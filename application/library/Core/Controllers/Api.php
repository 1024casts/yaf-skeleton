<?php

namespace Core\Controllers;


use Core\Exceptions\RuntimeException;
use Core\Http\Support\Defines\Code;
use Core\Http\Support\OutputTrait;
use Yaf\Exception\LoadFailed\Action;

/**
 * Web相关的基础控制器
 */
class Api extends Base
{
    use OutputTrait;

    /**
     * 初始化
     */
    public function init()
    {
        set_exception_handler([$this, 'handleException']);

        parent::init();
    }

    /**
     * 异常处理
     *
     * @param \Exception $e 异常
     * @return bool
     */
    public function handleException($e)
    {
        if ($e instanceof Action) {
            return $this->error(Code::NOT_FOUND);
        }

        return $this->error($e->getCode(), $e->getMessage());
    }

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
     * @param array  $varArray
     *
     * @return bool|void
     * @throws \Core\Exceptions\RuntimeException
     */
    public function display($actionName, array $varArray = [])
    {
        throw new RuntimeException('Abandon method!');
    }
}