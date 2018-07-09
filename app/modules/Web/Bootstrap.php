<?php

namespace Web;

use Yaf\Bootstrap_Abstract;
use Yaf\Dispatcher;
use Yaf\Loader;
use Yaf\Registry;
use Yaf\Request_Abstract;

use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\Engines\CompilerEngine;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\Engines\PhpEngine;
use Illuminate\View\FileViewFinder;

use PHPCasts\Yaf\Views\Adapter\Dispatcher as BladeDispatcher;
use PHPCasts\Yaf\Views\Adapter\BladeAdapter;
use PHPCasts\Yaf\Views\Adapter\TwigAdapter;


class Bootstrap extends Bootstrap_Abstract
{
    /**
     * @var \PHPCasts\Yaf\Di\ContainerInterface|mixed
     */
    private $container;

    private $ctrl;
    private $ctrlCls;
    private $act;

    public function __construct()
    {
        $this->container = Registry::get('container');
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
        $uid = $this->container->get('srv.user')->getCurrentUid();
        if (!$uid) {
            // throw new \Exception('用户未登录', Code::AUTH_FAILED);
        }

        $userInfo = $this->container->get('srv.user')->getCurrentUserInfo();
        $this->container->get('sessionBag')->set('userInfo', $userInfo);
        $this->container->get('sessionBag')->set('uid', $uid);
    }

    /**
     * @param Dispatcher $dispatcher
     */
    public function _initView(Dispatcher $dispatcher)
    {
        $config = Registry::get('config');
        $modulesName = $dispatcher->getRequest()->module;
        $viewPath = APP_PATH . '/modules/' . $modulesName . '/views';

        // twig模板引擎
        $viewEngine = $config['application']['view']['engine'];
        if ($viewEngine == 'twig') {
            $dispatcher->setView(new TwigAdapter($viewPath, $config['twig']));
        }
        // blade模板引擎
        elseif ($viewEngine == 'blade') {
            // finder实例
            $finder = new FileViewFinder(new Filesystem(), [$viewPath]);
            // 视图工厂
            $viewFactory = new BladeAdapter($this->registerEngineResolver(), $finder, new BladeDispatcher());
            // 设置模板引擎
            $dispatcher->setView($viewFactory);
        }
    }

    /**
     * 注册模板引擎
     * @return EngineResolver
     */
    protected function registerEngineResolver()
    {
        $resolver = new EngineResolver;
        foreach (['php', 'blade'] as $engine) {
            $this->{'register'.ucfirst($engine).'Engine'}($resolver);
        }
        return $resolver;
    }
    /**
     * 注册PHP模板引擎
     *
     * @param  \Illuminate\View\Engines\EngineResolver  $resolver
     * @return void
     */
    protected function registerPhpEngine($resolver)
    {
        $resolver->register('php', function () {
            return new PhpEngine;
        });
    }
    /**
     * 注册blade模板引擎
     *
     * @param  \Illuminate\View\Engines\EngineResolver  $resolver
     * @return void
     */
    protected function registerBladeEngine($resolver)
    {
        $cache = Registry::get('config')['blade']['cache'];  //获取编译路径
        $bladeCompiler = new BladeCompiler(new Filesystem(), $cache); //实例blade模板编译类
        $resolver->register('blade', function () use ($bladeCompiler) {
            return new CompilerEngine($bladeCompiler);
        });
    }
}
