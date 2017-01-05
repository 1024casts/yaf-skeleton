<?php

/**
 * @name Bootstrap
 * @author qloog
 * @desc   所有在Bootstrap类中, 以_init开头的方法, 都会被Yaf调用,
 * @see    http://www.php.net/manual/en/class.yaf-bootstrap-abstract.php
 * 这些方法, 都接受一个参数:Yaf_Dispatcher $dispatcher
 * 调用的次序, 和申明的次序相同
 */

use Yaf\Bootstrap_Abstract;
use Yaf\Registry;
use Yaf\Loader;
use Yaf\Dispatcher;
use Yaf\Application;
use Illuminate\Events\Dispatcher as LDispatcher;
use Illuminate\Container\Container as LContainer;
use Illuminate\Database\Capsule\Manager as Capsule;

class Bootstrap extends Bootstrap_Abstract
{

    public $config;

    /**
     * 初始化一些配置信息
     */
    public function _initConfig()
    {
        // 保存配置
        $this->config = Application::app()->getConfig()->toArray();
        Registry::set('config', $this->config);
    }

    /**
     * @param Dispatcher $dispatcher
     */
    public function _initPlugin(Dispatcher $dispatcher)
    {
        $dispatcher->registerPlugin(new InitPlugin());
        //$dispatcher->registerPlugin(new XHProfPlugin());
    }

    /**
     * 注册路由
     *
     * @param Dispatcher $dispatcher
     */
    public function _initRoute(Dispatcher $dispatcher)
    {
        //在这里注册自己的路由协议,默认使用简单路由
        // 增加一些路由规则
        // 默认是 Yaf_Route_Static
        // 支持以下方式
        // Yaf_Route_Simple
        // Yaf_Route_Supervar
        // Yaf_Route_Static
        // Yaf_Route_Map
        // Yaf_Route_Rewrite
        // Yaf_Route_Regex

        $config = require(APP_ROOT . '/conf/routes.php');
        $dispatcher->getRouter()->addConfig($config);
    }

    /**
     * 初始化 composer autoload
     *
     * @param Yaf\Dispatcher $dispatcher
     */
    public function _initComposerAutoload(Dispatcher $dispatcher)
    {
        $loader = Loader::getInstance();

        $autoload = APP_ROOT . '/vendor/autoload.php';
        if (file_exists($autoload)) {
            $loader->import($autoload);
        }
    }

    /**
     * @param Dispatcher $dispatcher
     */
    public function _initView(Dispatcher $dispatcher)
    {
        //在这里注册自己的view控制器，例如smarty
        // 不自动渲染视图
        $dispatcher->autoRender(false);
    }

    /**
     * @param Dispatcher $dispatcher
     */
    //protected function _initTwig(Dispatcher $dispatcher)
    //{
    //    $dispatcher->setView(new Twig(APP_PATH . '/views/', $this->config->twig->toArray()));
    //}

    /**
     * 初始化 Eloquent ORM
     *
     * @param Dispatcher|\Yaf\Dispatcher $dispatcher
     */
    public function _initDefaultDbAdapter(Dispatcher $dispatcher)
    {
        $capsule = new Capsule();
        $db = $this->config['database'];
        $capsule->addConnection($db);
        $capsule->setEventDispatcher(new LDispatcher(new LContainer));
        $capsule->setAsGlobal();
        $capsule->bootEloquent();
    }
}
