<?php

use Yaf\Controller_Abstract;
use Yaf\Request\Http as Request_Http;
use Yaf\Request\Simple as Request_Simple;

class RequestController extends Controller_Abstract
{
    public function serverInfoAction()
    {
        $request = $this->getRequest();

        // output: Yaf_Request_Http
        echo "request class 所属实例: ";
        if ($request instanceof Request_Http) {
            echo "Yaf_Request_Http";
        } elseif ($request instanceof Request_Simple) {
            echo "Yaf_Request_Simple";
        } else {
            echo "yaf_Request_Abstract";
        }
        echo "<br/>";

        var_dump($this->getRequest()->getServer() === $_SERVER);
        var_dump($this->getRequest()->getEnv() === $_ENV);
        var_dump($this->getRequest()->getLanguage());

        exit;
    }

    /**
     * 获取请求参数
     */
    public function paramsAction()
    {
        $request = $this->getRequest();

        echo "get('test'))" . PHP_EOL;
        var_dump($request->get('test'));
        echo PHP_EOL . 'getQuery:' . PHP_EOL;
        var_dump($request->getQuery());
        echo 'getQuery:' . PHP_EOL;
        var_dump($request->getQuery('test'));
        echo 'getPost:' . PHP_EOL;
        var_dump($request->getPost());
        echo 'getPost:' . PHP_EOL;
        var_dump($request->getPost('test'));
        echo 'getParams:' . PHP_EOL;
        var_dump($request->getParams());
        echo 'getParam:' . PHP_EOL;
        var_dump($request->getParam('uid'));
        echo 'getRequestUri:' . PHP_EOL;
        var_dump($request->getRequestUri());
        echo 'getMethod:' . PHP_EOL;
        var_dump($request->getMethod());
        echo 'getBaseUri:' . PHP_EOL;
        var_dump($request->getBaseUri());
        echo 'getCookie:' . PHP_EOL;
        var_dump($request->getCookie());
        echo 'getFiles:' . PHP_EOL;
        var_dump($request->getFiles());

        exit;
    }

    /**
     * 获取请求类型
     */
    public function methodAction()
    {
        var_dump($this->getRequest()->isCli());
        var_dump($this->getRequest()->isGet());
        var_dump($this->getRequest()->isPost());
        var_dump($this->getRequest()->isPut());
        var_dump($this->getRequest()->isXmlHttpRequest());
        var_dump($this->getRequest()->isHead());
        var_dump($this->getRequest()->isOptions());

        exit;
    }

    /**
     * 获取分发相关的方法
     */
    public function dispatchAction()
    {
        $this->getRequest()->setModuleName('Api');
        var_dump($this->getRequest()->getModuleName());

        $this->getRequest()->setControllerName('Index');
        var_dump($this->getRequest()->getControllerName());

        $this->getRequest()->setActionName('xxx');
        var_dump($this->getRequest()->getActionName());

        var_dump($this->getRequest()->getException());

        $this->getRequest()->setDispatched();
        var_dump($this->getRequest()->isDispatched());

        $this->getRequest()->setRouted();
        var_dump($this->getRequest()->isRouted());

        // 有获取就有set相关的方法
        exit;
    }
}
