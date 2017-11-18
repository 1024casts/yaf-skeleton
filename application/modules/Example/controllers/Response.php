<?php

use PHPCasts\Mvc\Controller\Api;
use Yaf\Dispatcher;

class ResponseController extends Api
{
    public function indexAction()
    {

        return $this->success(['user_id' => 1, 'name' => 'test1']);
    }
}
