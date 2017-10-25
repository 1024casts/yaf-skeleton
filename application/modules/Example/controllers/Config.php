<?php 

use Yaf\Controller_Abstract;

class ConfigController extends Controller_Abstract
{

	public function method1Action()
	{
		$config = Yaf\Application::app()->getConfig();

        // 默认是对象: object( yaf\config\ini)
        // $config->toArray() 转换为数组
        var_dump($config);
        exit;
	}

    public function method2Action()
    {
        // all
        $config =  new Yaf\Config\Ini(APP_ROOT . '/conf/application.ini');

        // section
        $config =  new Yaf\Config\Ini(APP_ROOT . '/conf/application.ini', 'develop');

        var_dump($config->toArray());
        exit;
	}

    public function method3Action()
    {
        \Yaf\Registry::set('demo', ['id'=>1,'name'=>'test']);
        $config = \Yaf\Registry::get('demo');

        var_dump($config);
        exit;
	}
}
