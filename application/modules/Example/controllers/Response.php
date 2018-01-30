<?php

use Yaf\Controller_Abstract;
use Yaf\Response\Http as Response_Http;

class ResponseController extends Controller_Abstract
{
    public function init()
    {
        // 禁用模板渲染
        Yaf\Dispatcher::getInstance()->disableView();
    }

    public function indexAction()
    {
        $response = $this->getResponse();
        var_dump(get_class_methods($response));

        // output: Yaf_Request_Http
        echo "response class 所属实例: ";
        if ($response instanceof Response_Http) {
            echo "Yaf_Response_Http";
        } else {
            echo "yaf_Response_Abstract";
        }
        echo "<br/>";

        // Pro tips: 没传key的都用默认key: Response_Http::DEFAULT_BODY, 可以自定义
        $response->setBody("Hello")->setBody(" World", "footer");
        $response->prependBody("Prepend ");
        $response->appendBody(" Append ");
        $response->setHeader('Yaf-Version', "3.0.4");
        //$response->setAllHeaders(['Yaf-Version' => "3.0.4", "PHP-Version" => 7.0]);
        //$response->setRedirect("http://www.baidu.com");

        var_dump($response->getBody()); //default
        var_dump($response->getBody(Response_Http::DEFAULT_BODY)); //same as above
        var_dump($response->getBody("footer"));
        var_dump($response->getBody(NULL)); //get all

        $response->response();
    }

    public function httpAction()
    {
        var_dump(get_class_methods(Yaf\Response\Http::class));
    }

    public function cliAction()
    {
        var_dump(get_class_methods(Yaf\Response\Cli::class));
    }
}
