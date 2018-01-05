<?php 

use Yaf\Controller_Abstract;

class RouteController extends Controller_Abstract
{

    public function init()
    {
        \Yaf\Dispatcher::getInstance()->disableView();
    }
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
     * @example: http://yaf-skel.com/index.php?m=example&c=route&a=simple
     */
    public function simpleAction()
    {
        \Yaf\Dispatcher::getInstance()->disableView();

        echo 'I am a simple route';
	}

    /**
     * supervar 路由
     *
     * @example: http://yaf-skel.com/index.php?r=/example/route/supervar
     */
    public function supervarAction()
    {
        \Yaf\Dispatcher::getInstance()->disableView();

        echo 'I am a supervar route';
	}

    /**
     * rewrite 路由
     *
     * @example: http://yaf-skel.com/product/iphone
     *
     *           http://yaf-skel.com/web/product/iphone
     *
     */
    public function rewriteAction()
    {
        echo 'I am a rewrite route' . '<br/>';

        // 获取参数
        echo 'matched param: ' . $this->getRequest()->getParam('name');
	}

    /**
     * 正则路由
     *
     *  @example: http://yaf-skel.com/product/1/2?a=1
     */
    public function regexAction()
    {
        echo 'I am a regex route' . PHP_EOL;

        // 获取参数
        echo 'id matched param: ' . $this->getRequest()->getParam('id');
        echo 'tag id matched param: ' . $this->getRequest()->getParam('tag_id');
        echo '<br>';
        echo 'a value is:' . $this->getRequest()->getQuery('a');
	}

    /**
     * map 路由
     */
    public function mapAction()
    {
        echo 'I am a map route' . PHP_EOL;
        exit;
	}

    /**
     * 自定义路由
     */
    public function customAction()
    {
        
	}
}
