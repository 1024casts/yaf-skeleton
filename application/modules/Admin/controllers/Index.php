<?php

use PHPCasts\Mvc\Controller\Web;

/**
 * 首页
 *
 */
class IndexController extends Web
{

    public function indexAction()
    {
        echo 'Hello World!';
    }
}