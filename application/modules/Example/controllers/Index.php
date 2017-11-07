<?php 

use Yaf\Controller_Abstract;
use PHPCasts\Caches\Cache;

class IndexController extends Controller_Abstract
{
	public function indexAction()
	{
		//here to write request;
		//here to call business logic;
		//here to write response;
        Cache::getInstance()->get();
	}
}
