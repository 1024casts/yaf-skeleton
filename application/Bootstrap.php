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
use PHPCasts\Di\Container;
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

        if (ini_get('yaf.environ') != 'production') {
            $dispatcher->registerPlugin(new QueryLogPlugin());
        }
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
        $config = require(APP_ROOT . '/conf/routes.php');
        $dispatcher->getRouter()->addConfig($config);

        // or
        //$config = new \Yaf\Config\Ini(APP_PATH . '/conf/route.ini', 'common');
        //if ($config->routes) {
        //    $dispatcher->getRouter()->addConfig($config->routes);
        //}
    }

    /**
     * 初始化依赖注入services
     */
    public function _initServices()
    {
        $services = [];
        if (file_exists($basicService = APP_CONFIG_PATH . '/di.php') && is_readable($basicService)) {
            $services = require $basicService;
        }

        $env = Application::app()->environ();
        if (file_exists($envService = APP_CONFIG_PATH . '/' . $env . '/di.php') && is_readable($envService)) {
            $services = array_merge($services, require $envService);
        }

        Registry::set('di', new Container($services));
    }

    /**
     * 初始化全局事件监听
     */
    public function _initListener()
    {
        $listeners = [];
        if (file_exists($basicListener = APP_CONFIG_PATH . '/listener.php')) {
            $listeners = require $basicListener;
        }

        $env = Application::app()->environ();
        if (file_exists($envListener = APP_CONFIG_PATH . '/' . $env . '/listener.php') && is_readable($envListener)) {
            $listeners = array_merge($listeners, require $envListener);
        }

        $em = Registry::get('di')->get('eventsManager');
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
        //在这里注册自己的view控制器，例如smarty
        if ($dispatcher->getRequest()->getMethod() === 'CLI' ) {
            // 不自动渲染视图
            $dispatcher->autoRender(false);
        }
    }

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

        class_alias('\Illuminate\Database\Capsule\Manager', 'DB');

        // todo: 记录执行的sql
        // see: https://github.com/JustPoet/eyaf
        if (ini_get('yaf.environ') != 'production') {
            $capsule->getConnection()->enableQueryLog();
        }
    }
}
