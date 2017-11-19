<?php 

use Yaf\Controller_Abstract;

class RouteController extends Controller_Abstract
{
    /**
     * 静态路由
     */
    public function staticAction()
    {
        \Yaf\Dispatcher::getInstance()->disableView();

        echo 'PHPCasts';
	}

    /**
     * simple 路由
     */
    public function simpleAction()
    {
        
	}

    /**
     * supervar 路由
     */
    public function supervarAction()
    {
        
	}

    /**
     * rewrite 路由
     */
    public function rewriteAction()
    {
        
	}

    /**
     * 正则路由
     */
    public function regexAction()
    {
        
	}

    /**
     * map 路由
     */
    public function mapAction()
    {
        
	}

    /**
     * 自定义路由
     */
    public function customAction()
    {
        
	}
}
