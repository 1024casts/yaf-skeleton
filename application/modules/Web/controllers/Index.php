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

        // 如果不调用该代码，display后，框架还会再自动渲染代码一次，导致重复渲染
        // TODO: 放到web基类里
        Yaf\Dispatcher::getInstance()->autoRender(false);
        // or
        //Yaf\Dispatcher::getInstance()->disableView();
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
        $data = ['message'=> 'Hello Yaf!', 'name'=>'user2'];

        $this->display('test', $data);
    }
}