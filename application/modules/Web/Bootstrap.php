<?php

namespace Web;

use PHPCasts\Views\Twig;
use Yaf\Bootstrap_Abstract;
use Yaf\Dispatcher;
use Yaf\Loader;
use Yaf\Registry;
use Yaf\Request_Abstract;

class Bootstrap extends Bootstrap_Abstract
{
    /**
     * @var \Core\Di\ContainerInterface|mixed
     */
    private $di;

    private $ctrl;
    private $ctrlCls;
    private $act;

    public function __construct()
    {
        $this->di = Registry::get('di');
    }

    public function _initAuth(Dispatcher $dispatcher)
    {
        $this->loadCtrlCls($dispatcher->getRequest());

        if ($this->isIgnoreUserAuth()) {
            return;
        }

        $this->setLoginedUser();
    }

    private function loadCtrlCls(Request_Abstract $request)
    {
        $this->ctrl = $request->getControllerName();
        $this->act = $request->getActionName();

        $ctrlFile = APP_PATH . '/modules/' . $request->getModuleName() . '/controllers/' . $this->ctrl . '.php';
        Loader::import($ctrlFile);

        $this->ctrlCls = $this->ctrl.'Controller';
    }

    private function isIgnoreUserAuth()
    {
        $ref = new \ReflectionClass($this->ctrlCls);
        if (!$ref->hasProperty('ignoreUserAuth')) {
            return false;
        }

        $prop = $ref->getProperty('ignoreUserAuth');
        $prop->setAccessible(true);

        $propVal = $prop->getValue();
        if ($propVal === false) {
            return true;
        }

        if (is_array($propVal) && in_array($this->act, $propVal)) {
            return true;
        }

        return false;
    }

    private function setLoginedUser()
    {
        $uid = $this->di->get('srv.user')->getCurrentUid();
        if (!$uid) {
            // throw new \Exception('用户未登录', Code::AUTH_FAILED);
        }

        $userInfo = $this->di->get('srv.user')->getCurrentUserInfo();
        $this->di->get('sessionBag')->set('userInfo', $userInfo);
        $this->di->get('sessionBag')->set('uid', $uid);
    }

    /**
     * @param Dispatcher $dispatcher
     */
    public function _initTwig(Dispatcher $dispatcher)
    {
        $config = Registry::get('config');

        // twig模板引擎
        $viewEngine = $config['application']['view']['engine'];
        if ($viewEngine == 'twig') {
            $modulesName = $dispatcher->getRequest()->module;
            $path = [APP_PATH . '/modules/' . $modulesName . '/views'];
            $dispatcher->setView(new Twig($path, $config['twig']));
        }
        // blade模板引擎
        elseif ($viewEngine == 'blade') {

        }
    }
}
