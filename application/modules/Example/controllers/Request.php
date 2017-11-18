<?php

use Yaf\Controller_Abstract;

class RequestController extends Controller_Abstract
{
    public function serverInfoAction()
    {
        //var_dump($this->getRequest()->getServer(), $_SERVER);
        var_dump($this->getRequest()->getEnv(), $_ENV);
        var_dump($this->getRequest()->getLanguage());

        exit;
    }

    /**
     * 获取请求参数
     */
    public function paramsAction()
    {
        var_dump($this->getRequest()->get('test'));
        var_dump($this->getRequest()->getQuery());
        var_dump($this->getRequest()->getQuery('test'));
        var_dump($this->getRequest()->getPost());
        var_dump($this->getRequest()->getPost('test'));
        var_dump($this->getRequest()->getParams());
        var_dump($this->getRequest()->getParam('uid'));

        var_dump($this->getRequest()->getRequestUri());
        var_dump($this->getRequest()->getMethod());
        var_dump($this->getRequest()->getBaseUri());
        var_dump($this->getRequest()->getCookie());
        var_dump($this->getRequest()->getFiles());

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
