<?php 

use Yaf\Controller_Abstract;

class RouteController extends Controller_Abstract
{
    /**
     * 静态路由
     *
     * @example: http://yaf-skel.com/example/route/static
     */
    public function staticAction()
    {
        \Yaf\Dispatcher::getInstance()->disableView();

        echo 'PHPCasts';
	}

    /**
     * simple 路由
     *
     * @example: http://yaf-skel.com/example/route/simple
     */
    public function simpleAction()
    {
        \Yaf\Dispatcher::getInstance()->disableView();

        echo 'I am a simple route';
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
