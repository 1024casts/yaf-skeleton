<?php

use Yaf\Loader;
use Core\Bootstrap;

/**
 * 初始化插件
 */
class InitPlugin extends Yaf\Plugin_Abstract
{
    /**
     * 路由结束时
     *
     * @param \Yaf\Request_Abstract  $request
     * @param \Yaf\Response_Abstract $response
     * @return bool
     */
    public function routerShutdown(Yaf\Request_Abstract $request, Yaf\Response_Abstract $response)
    {
        if ($request->module == 'Index') {
            $request->setModuleName('Home');
        }

        $bootsFile = APP_PATH . '/modules/' . $request->module . '/Bootstrap.php';
        if (file_exists($bootsFile)) {
            Loader::import($bootsFile);
            Bootstrap::boot('\\' . $request->module . '\\Bootstrap');
        }

        return true;
    }
}
