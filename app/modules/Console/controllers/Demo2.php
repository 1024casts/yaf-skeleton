<?php

use App\Defines\Code;
use PHPCasts\Yaf\Mvc\Controller\Console;
use PHPCasts\Yaf\Di\InjectionWareTrait;
use PHPCasts\Yaf\Events\ManagerWareTrait;
use PHPCasts\Yaf\Log\Log;

/**
 * 主要是如何执行脚本
 *
 * Class Demo2Controller
 */
class Demo2Controller extends Console
{
    use InjectionWareTrait;
    use ManagerWareTrait;

    // @attention \Yaf\Controller_Abstract源码(2.3.5)中声明了该宏变量但没有定义该属性, 会导致InjectionWareTrait::__get方法获取不到对应服务从而触发异常
    public $yafAutoRender = false;

    protected $code = Code::class;

    public function init()
    {
        $this->strLockFile = '/tmp/'.__CLASS__.'.lock';
    }

    public function usage()
    {
        global $argv;

        echo sprintf('Usage: %s %s -uid=123 [-username=user1]', $argv[0], $argv[1]), PHP_EOL;
    }

    /**
     * 执行脚本
     *
     * @desc 执行： php bin/run Demo2/start
     */
    public function startAction()
    {
        // 获取参数
        $uid = $this->getRequest()->getParam('uid', 0);

        // 校验参数
        if (!$uid) {
            $this->usage();

            return false;
        }

        $message = 'demo2_test';

        Log::notice("{$message} start...");

        // here write your the logic of business
        $ret = parent::init();
        if ($ret === false) {
            Log::warning("{$message} init failed");
            return false;
        }

        $ret = $this->exec();
        if ($ret === false) {
            Log::warning("{$message} exec failed");
            return false;
        }

        $ret = $this->over();
        if ($ret === false) {
            Log::warning("{$message} over failed");
            return false;
        }

        return true;
    }

    // 具体的逻辑处理
    public function exec()
    {
        while (1) {
            /* 当前时间 */
            $this->intNowTime = time();

            /* 执行完毕 */
            if ($this->intNowTime > $this->intOverTime) {
                break;
            }

            /* 执行 */
            $this->_exec();

            /* 执行Sleep */
            sleep(5);
        }

        return true;
    }

    /**
     * 具体的业务逻辑处理
     */
    public function _exec()
    {

        echo time() . PHP_EOL;

    }


}
