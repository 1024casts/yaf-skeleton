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
use Init\XHProfPlugin;
use Support\Log;
use Monolog\Logger;
use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
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
        //把配置保存起来
        $this->config = Application::app()->getConfig();
        Registry::set('config', $this->config);
    }

    /**
     * @param Dispatcher $dispatcher
     */
    public function _initPlugin(Dispatcher $dispatcher)
    {
        //$dispatcher->registerPlugin(new XHProfPlugin());
    }

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
    }

    public function _initView(Dispatcher $dispatcher)
    {
        //在这里注册自己的view控制器，例如smarty,firekylin
    }

    /**
     * 初始化本地类库的名称空间 Biz Ns
     * 例如本地类库 Biz_Test, Ns\Test 放在library目录下
     * library/biz/Test.php
     * library/ns/Test.php
     */
    public function _initRegisterLocalClass(Yaf\Dispatcher $dispatcher)
    {
        $loader = Yaf\Loader::getInstance();
        $loader->registerLocalNamespace(array('Core'));
    }

    /**
     * 初始化 composer autoload
     *
     * @param Yaf\Dispatcher $dispatcher
     */
    public function _initComposerAutoload(Dispatcher $dispatcher)
    {
        $autoload = APP_ROOT . '/vendor/autoload.php';
        if (file_exists($autoload)) {
            Loader::import($autoload);
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
        $db = $this->config->database->toArray();
        $capsule->addConnection($db);
        $capsule->setEventDispatcher(new LDispatcher(new LContainer));
        $capsule->setAsGlobal();
        $capsule->bootEloquent();
    }

    /**
     * 错误开关
     */
    public function _initErrors()
    {
        //报错是否开启
        if ($this->config->application->showErrors) {
            error_reporting(-1);
            ini_set('display_errors', 'On');
        } else {
            error_reporting(0);
            ini_set('display_errors', 'Off');
        }
        //set_error_handler(['Error', 'errorHandler']);
    }

    /**
     * Initialize logger.
     */
    public function _initLogger()
    {
        if (Log::hasLogger()) {
            return;
        }
        $logger = new Logger('yaf-api');
        if (!$this->config->application->debug || defined('PHPUNIT_RUNNING')) {
            $logger->pushHandler(new NullHandler());
        } elseif ($logFile = $this->config->log->directory) {
            $logger->pushHandler(new StreamHandler($logFile . date('Y-m-d') . '.log', $this->config->log->level));
        }
        Log::setLogger($logger);
    }

    /**
     * 读取相应的配置初始化XHProf
     *
     * @access public
     * @param \Yaf\Dispatcher $dispatcher
     * @return void
     */
    public function _initXHProf(Dispatcher $dispatcher)
    {
        //if (isset($this->config->xhprof)) {
        //    $xhprof_config = $this->config->xhprof->toArray();
        //    if (extension_loaded('xhprof') &&  $xhprof_config && isset($xhprof_config['open']) && $xhprof_config['open'] ) {
        //        $default_flags = XHPROF_FLAGS_CPU | XHPROF_FLAGS_MEMORY;
        //        $ignore_functions = isset($xhprof_config['ignored_functions']) && is_array($xhprof_config['ignored_functions']) ? $xhprof_config['ignored_functions'] : array();
        //        if (isset($xhprof_config['flags'])) {
        //            xhprof_enable($xhprof_config['flags'], $ignore_functions);
        //        } else {
        //            xhprof_enable($default_flags, $ignore_functions);
        //        }
        //    }
        //}
    }
}
