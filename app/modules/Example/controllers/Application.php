<?php 

use Yaf\Controller_Abstract;

class ApplicationController extends Controller_Abstract
{
	public function indexAction()
	{

	    // config
        Yaf\Application::app()->getConfig();

        // modules
        Yaf\Application::app()->getModules();

        // dispatcher
        Yaf\Application::app()->getDispatcher();

	}
}
