<?php

use PHPCasts\Mvc\Controller\Api;

class IndexController extends Api
{
    public function testAction()
    {
        Yaf\Dispatcher::getInstance()->autoRender(false);
        $this->success();
    }
}