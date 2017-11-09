<?php 

use Yaf\Controller_Abstract;

class AutoloadController extends Controller_Abstract
{
	public function indexAction()
	{
	    Yaf\Dispatcher::getInstance()->disableView();

        Yaf\Loader::import(APP_PATH . '/library/helper.php');

        test();

	}
}
