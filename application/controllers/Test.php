<?php

use PHPCasts\Log\Log;
use Yaf\Request\Http;

class TestController extends Core\Mvc\Controller\Web
{
    /**
     * 初始化由yaf自动调用
     * @access public
     */
    public function init()
    {
        parent::init();
    }

    public function testAction()
    {
        $user = (new UserModel());

        var_dump($user->first());
    }
}
