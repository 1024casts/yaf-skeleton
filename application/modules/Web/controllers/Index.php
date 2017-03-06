<?php

class IndexController extends BaseController
{

    /**
     * 忽略用户登录的action
     *
     * @var array
     */
    protected static $ignoreUserAuth = ['test'];

    /**
     * 首页
     *
     * @return string
     */
    public function indexAction()
    {
        $data = [
            'message' => 'test message'
        ];

        return $this->display('index', $data);
    }

    public function testAction()
    {

    }
}