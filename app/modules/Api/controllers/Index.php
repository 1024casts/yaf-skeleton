<?php

use PHPCasts\Mvc\Controller\Api;

class IndexController extends Api
{
    public function testAction()
    {
        Yaf\Dispatcher::getInstance()->autoRender(false);
        $data = ['uid'=>1, 'name' => 'test']; // 你的业务数据
        $this->success($data);
    }
}