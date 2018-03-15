<?php

use Yaf\Controller_Abstract;
use Yaf\Response\Http as Response_Http;

class ResponseController extends Controller_Abstract
{

    public function init()
    {
        Yaf\Dispatcher::getInstance()->disableView();
    }

    public function indexAction()
    {
        $response = $this->getResponse();

        $response->setBody("test content for phpcasts");

        $response->response();
    }
}
