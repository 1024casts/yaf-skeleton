<?php

use Core\Mvc\Controller\Web;

class IndexController extends Web
{

    /**
     * 忽略用户登录的action
     *
     * @var array
     */
    protected static $ignoreUserAuth = ['test','index'];

    /**
     * 首页
     *
     * @return string
     */
    public function indexAction()
    {
        echo \Yaf\ENVIRON;
        return $this->display('hello');
    }

    public function testAction()
    {
        $data = [
            'message' => 'test message'
        ];

        return $this->display('index', $data);
    }
}