<?php

namespace Tests;

define('APP_ROOT', dirname(__DIR__));
define('APP_PATH', APP_ROOT . '/application');
define('APP_CONFIG_PATH', APP_ROOT . '/conf');

use Yaf\Application;
use Yaf\Registry;

/**
 * 测试基类
 */
class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * yaf运行实例
     *
     * @var Application
     */
    protected $_application = null;

    /**
     * 构造方法，初始化yaf运行实例
     */
    public function __construct()
    {
        $this->_application = $this->getApplication();
        parent::__construct();
    }

    /**
     * 设置application
     */
    public function setApplication()
    {
        $application = new Application(APP_CONFIG_PATH . "/application.ini");
        $application->bootstrap();
        Registry::set('application', $application);
    }

    /**
     * 获取application
     *
     * @return Application
     */
    public function getApplication()
    {
        $application = Registry::get('application');
        if (!$application) {
            $this->setApplication();
        }

        return Registry::get('application');
    }
}