<?php

use Yaf\Controller_Abstract;
use Yaf\Request\Http as Request_Http;
use Yaf\Request\Simple as Request_Simple;

/**
 * Yaf\Request\Http 和 Yaf\Request\Simple 包含的方法是一样的
 * Class RequestController
 */
class RequestController extends Controller_Abstract
{

    public function init()
    {
        // 禁用模板渲染
        Yaf\Dispatcher::getInstance()->disableView();
    }

    public function indexAction()
    {
        var_dump($this->getRequest()->getParam('uid'));
        //var_dump(get_class_methods(Yaf\Request\Http::class));
    }

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

        var_dump($this->getRequest()->getServer() === $_SERVER); // true
        var_dump($this->getRequest()->getEnv() === $_ENV); // true
        var_dump($this->getRequest()->getLanguage()); //
    }

    /**
     * 获取请求参数
     */
    public function paramsAction()
    {
        $request = $this->getRequest();

        echo '<pre>';

        // http://yaf-skel.com/example/request/params?user=test&uid=1

        var_dump($request->get('user'));        // test
        var_dump($request->getQuery());         // ['user' => test, 'uid' => 1]
        var_dump($request->getQuery('user'));   // test
        var_dump($request->getPost());          // empty
        var_dump($request->getPost('user'));    // null

        var_dump($request->getRequestUri());    // /example/request/params
        var_dump($request->getMethod());        // GET
        var_dump($request->getBaseUri());       // ''
        var_dump($request->getCookie());        // emtpy
        var_dump($request->getFiles());         // empty

        // http://yaf-skel.com/example/request/params/user/test/uid/1
        var_dump($request->getParams());         // ['user' => test, 'uid' => 1]
        var_dump($request->getParam('uid'));     // 1
        var_dump($request->getRequestUri());     // /example/request/params/user/test/uid/1

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
        var_dump($this->getRequest()->isXmlHttpRequest()); // 是否为ajax请求
        var_dump($this->getRequest()->isHead());
        var_dump($this->getRequest()->isOptions());
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
    }

}
