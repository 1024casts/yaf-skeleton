<?php

use PHPCasts\Mvc\Controller\Base;

/**
 *
 * 此控制器主要用来测试twig模板引擎
 *
 * Class TwigController
 */
class BladeController extends Base
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
    public function testAction($name = "yaf")
    {

        $list = [['name'=>'ZhangSan', 'age'=>18], ['name'=>'Lisi', 'age'=>20]];

        $this->getView()->display("blade.test", compact('list','name'));

        exit;
    }
}