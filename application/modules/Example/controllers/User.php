<?php

class UserController extends Yaf\Controller_Abstract
{

    public function init()
    {
        Yaf\Dispatcher::getInstance()->disableView();
        echo 'init...';
    }

}