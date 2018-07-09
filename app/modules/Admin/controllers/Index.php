<?php

use PHPCasts\Yaf\Mvc\Controller\Web;
/**
 * 首页
 *
 */
class IndexController extends Web
{

    public function init()
    {
        Yaf\Dispatcher::getInstance()->autoRender(false);
        //Yaf\Dispatcher::getInstance()->disableView();
    }

    public function indexAction()
    {
        echo 'Hello World!';
    }
}