<?php

use Core\Mvc\Controller\Web;
use App\Services\UserService;

/**
 * 基础控制器
 */
class BaseController extends Web
{
    /**
     * 当前登录的用户信息
     *
     * @var array
     */
    protected $loginUser = [];

    /**
     * 初始化
     */
    public function init()
    {
        parent::init();

        $this->loginUser = UserService::getLoginInfo();
    }

    /**
     * 公共模板变量
     *
     * @return array
     */
    public function commonVars()
    {
        return [
            'assets' => '/',
            'userInfo' => $this->loginUser,
        ];
    }
}