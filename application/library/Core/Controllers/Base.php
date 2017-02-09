<?php

namespace Core\Controllers;

use Yaf\Controller_Abstract;
use Yaf\Dispatcher;
use Yaf\Registry;

/**
 * 基础控制器,所有控制器都应该实现此类
 */
abstract class Base extends Controller_Abstract
{
    /**
     * 是否自动检查请求,例如是否为Ajax/Json
     *
     * @var bool
     */
    protected $autoCheckRequest = true;

    /**
     * 配置文件
     *
     * @var array
     */
    public $config = [];

    /**
     * 初始化
     */
    public function init()
    {
        $this->config = Registry::get('config');

        $request = $this->getRequest();
        if (
            $this->autoCheckRequest
            && (
                $request->isXmlHttpRequest()
                || strpos($request->getServer('HTTP_ACCEPT', 'text/html'), '/json') !== false
            )
        ) {
            $this->getResponse()->setHeader('Content-Type', 'application/json');

            Dispatcher::getInstance()->disableView();
        }
    }

    /**
     * 返回当前模块名
     *
     * @access protected
     * @return string
     */
    protected function getModule()
    {
        return $this->getRequest()->module;
    }

    /**
     * 返回当前控制器名
     *
     * @access protected
     * @return string
     */
    protected function getController()
    {
        return $this->getRequest()->controller;
    }

    /**
     * 返回当前动作名
     *
     * @access protected
     * @return string
     */
    protected function getAction()
    {
        return $this->getRequest()->action;
    }

    /**
     * 获取GET数据
     *
     * @param string $param
     * @param mixed|null $defaultValue
     *
     * @return mixed
     */
    protected function getQuery($param = null, $defaultValue = null)
    {
        if ($param === null) {
            return $this->getRequest()->getQuery();
        }

        return $this->getRequest()->getQuery($param, $defaultValue);
    }

    /**
     * 获取参数数据
     *
     * @param string $param
     * @param mixed|null $defaultValue
     *
     * @return mixed
     */
    protected function getParam($param = null, $defaultValue = null)
    {
        $request = $this->getRequest();
        if ($param === null) {
            return array_merge($request->getQuery(), $request->getParams());
        }

        $value = $request->getParam($param);
        if ($value === null) {
            return $request->getQuery($param, $defaultValue);
        }

        return $value;
    }

    /**
     * 获取POST数据
     *
     * @param string $param
     * @param mixed|null $defaultValue
     *
     * @return mixed
     */
    protected function getPost($param = null, $defaultValue = null)
    {
        if ($param === null) {
            return $this->getRequest()->getPost();
        }

        return $this->getRequest()->getPost($param, $defaultValue);
    }

    /**
     * 获取$_REQUEST数据
     *
     * @todo escape & filter
     *
     * @param string $param
     * @param mixed|null $defaultValue
     *
     * @return mixed
     */
    public function get($param = null, $defaultValue = null)
    {
        if ($param === null) {
            return $_REQUEST;
        }

        return isset($_REQUEST[$param]) ? $_REQUEST[$param] : $defaultValue;
    }

    /**
     * 获取cookie数据
     *
     * @param string $param
     * @param mixed|null $defaultValue
     *
     * @return mixed
     */
    protected function getCookie($param = null, $defaultValue = null)
    {
        if ($param === null) {
            return $this->getRequest()->getCookie();
        }

        return $this->getRequest()->getCookie($param, $defaultValue);
    }

    /**
     * 获取SERVER数据
     *
     * @param string $param
     * @param mixed|null $defaultValue
     *
     * @return mixed
     */
    protected function getServer($param = null, $defaultValue = null)
    {
        if ($param === null) {
            return $this->getRequest()->getServer();
        }

        return $this->getRequest()->getServer($param, $defaultValue);
    }

    /**
     * 请求发放: GET,POST,HEAD,PUT,CLI
     *
     * @return mixed
     */
    protected function getMethod()
    {
        return $this->getRequest()->getMethod();
    }

    /**
     * 是否PUT操作
     *
     * @return mixed
     */
    protected function isPut()
    {
        return $this->getRequest()->isPut();
    }

    /**
     * 是否DELETE
     *
     * @return mixed
     */
    protected function isDelete()
    {
        return $this->getRequest()->getServer('REQUEST_METHOD') == 'DELETE';
    }

    /**
     * 是否GET
     *
     * @return mixed
     */
    protected function isGet()
    {
        return $this->getRequest()->isGet();
    }

    /**
     * 是否POST
     *
     * @return mixed
     */
    protected function isPost()
    {
        return $this->getRequest()->isPost();
    }

    /**
     * 是否AJAX
     *
     * @return mixed
     */
    protected function isAjax()
    {
        return $this->getRequest()->isXmlHttpRequest();
    }
}