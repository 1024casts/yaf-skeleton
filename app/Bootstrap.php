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

use PHPCasts\Yaf\ServiceContainer;
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

        if (empty($_SERVER['HTTP_X_REQUEST_ID'])) {
            $_SERVER['HTTP_X_REQUEST_ID'] = uniqid();
        }

        Registry::set('config', $this->config);
    }

    /**
     * 初始化Loader
     */
    public function _initLoader()
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
    public function _initPlugin(Dispatcher $dispatcher)
    {
        $dispatcher->registerPlugin(new ModuleBootstrapPlugin());
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

        // 开启simple路由协议
        //$router = $dispatcher->getRouter();
        //$route = new Yaf\Route\Simple("m","c","a");
        //$router->addRoute('name', $route);

        // 开启supervar路由协议
        //$router = $dispatcher->getRouter();
        //$route = new Yaf\Route\Supervar('r');
        //$router->addRoute('name', $route);

        // 开启rewrite路由协议
        //$router = $dispatcher->getRouter();
        //$route = new Yaf\Route\Rewrite(
        //    '/product/:name',
        //    [
        //        'module' => 'example',
        //        'controller' => 'route',
        //        'action' => 'rewrite'
        //    ]
        //);
        //$router->addRoute('name', $route);

        // 开启regex路由协议
        //$router = $dispatcher->getRouter();
        //$route = new Yaf\Route\Regex(
        //    '/product\/([\d]+)\/([\d]+)/',
        //    [
        //        'module' => 'example',
        //        'controller' => 'route',
        //        'action' => 'regex'
        //    ],
        //    [
        //        1 => 'id',
        //        2 => 'tag_id'
        //    ]
        //);
        //$router->addRoute('name', $route);

        // 开启map路由协议
        //$router = $dispatcher->getRouter();
        //$route = new Yaf\Route\Map(true, '#');
        //$router->addRoute('product', $route);

        //$config = require(APP_ROOT . '/conf/routes.php');
        //$router = $dispatcher->getRouter();
        //$router->addConfig($config);
    }

    /**
     * 初始化依赖注入services
     */
    public function _initServices()
    {
        $services = [];
        if (file_exists($basicService = CONFIG_PATH . '/providers.php') && is_readable($basicService)) {
            $services = require $basicService;
        }

        $env = Application::app()->environ();
        if (file_exists($envService = CONFIG_PATH . '/' . $env . '/providers.php') && is_readable($envService)) {
            $services = array_merge($services, require $envService);
        }

        Registry::set('container', new ServiceContainer($services));
    }

    /**
     * 初始化全局事件监听
     */
    public function _initListener()
    {
        $listeners = [];
        if (file_exists($basicListener = CONFIG_PATH . '/listener.php')) {
            $listeners = require $basicListener;
        }

        $env = Application::app()->environ();
        if (file_exists($envListener = CONFIG_PATH . '/' . $env . '/listener.php') && is_readable($envListener)) {
            $listeners = array_merge($listeners, require $envListener);
        }

        /** @var \PHPCasts\Yaf\Events\Manager $em */
        $em = Registry::get('container')->get('eventsManager');
        foreach ($listeners as $event => $handler) {
            if (is_array($handler)) {
                foreach ($handler as $h) {
                    $em->attach($event, $h);
                }
            } else {
                $em->attach($event, $handler);
            }
        }
    }

    /**
     * @param Dispatcher $dispatcher
     */
    public function _initView(Dispatcher $dispatcher)
    {
        // 不自动渲染视图
        $dispatcher->autoRender(false);
    }

    /**
     * 初始化 Eloquent ORM
     *
     * @param Dispatcher|\Yaf\Dispatcher $dispatcher
     */
    public function _initDatabase(Dispatcher $dispatcher)
    {
        $capsule = new Capsule();
        $db = $this->config['database'];
        $capsule->addConnection($db);
        $capsule->setEventDispatcher(new LDispatcher(new LContainer));
        $capsule->setAsGlobal();
        $capsule->bootEloquent();

        class_alias('\Illuminate\Database\Capsule\Manager', 'DB');
    }
}
