<?php

use App\Defines\Code;
use PHPCasts\Mvc\Controller\Console;
use PHPCasts\Di\InjectionWareTrait;
use PHPCasts\Events\ManagerWareTrait;

class DemoController extends Console
{
    use InjectionWareTrait;
    use ManagerWareTrait;

    // @attention \Yaf\Controller_Abstract源码(2.3.5)中声明了该宏变量但没有定义该属性, 会导致InjectionWareTrait::__get方法获取不到对应服务从而触发异常
    public $yafAutoRender = false;

    protected $code = Code::class;

    /**
     * 启动队列worker
     */
    public function startAction()
    {
        $uid = $this->getRequest()->getParam('uid', 0);
        $username = $this->getRequest()->getParam('username');

        if (!$uid) {
            $this->usage();

            return;
        }

        // here write your the logic of business

    }

    public function usage()
    {
        global $argv;

        echo sprintf('Usage: %s %s -uid=123 [-username=user1]', $argv[0], $argv[1]), PHP_EOL;
    }

    /**
     * 执行方法： php bin/run Demo/response
     */
    public function responseAction()
    {
        var_dump(get_class_methods(Yaf\Response\Http::class));
    }
}
