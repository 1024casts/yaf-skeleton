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

    public function helloAction()
    {
        echo 'Hello World!';

        exit;
    }

    /**
     * @return bool|void
     */
    public function testAction()
    {
        $data = 'Hello Yaf!';

        $users = [['name'=> 'user1'], ['name'=>'user2']];

        $this->getView()->assign("content", $data);
        $this->getView()->assign("users", $users);

        return $this->display('test', ['content' => $data]);
    }
}