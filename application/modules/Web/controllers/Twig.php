<?php

/**
 *
 * 此控制器主要用来测试twig模板引擎
 *
 * Class TwigController
 */
class TwigController extends Yaf\Controller_Abstract
{

    /**
     * 忽略用户登录的action
     *
     * @var array
     */
    protected static $ignoreUserAuth = ['test','index'];

    /**
     * @return bool|void
     */
    public function testAction()
    {
        $data = 'Hello Yaf!';

        $users = [['name'=> 'user1'], ['name'=>'user2'], ['name'=>'user3']];

        $this->getView()->assign("content", $data);
        $this->getView()->assign("users", $users);
    }
}