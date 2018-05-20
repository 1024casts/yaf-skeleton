<?php

use PHPCasts\Mvc\Controller\Web;

class IndexController extends Web
{
    /**
     * 忽略用户登录的action
     *
     * @var array
     */
    protected static $ignoreUserAuth = ['test','index'];

    public function init()
    {
        parent::init();
    }

    public function helloAction()
    {
        echo 'Hello World!';
    }

    /**
     * @return bool|void
     */
    public function testAction()
    {
        $this->assign('message', 'Hello Yaf');

        $this->display('test');
    }
}