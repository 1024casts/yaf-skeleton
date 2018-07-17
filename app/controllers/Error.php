<?php

use PHPCasts\Yaf\Mvc\Controller\Web;
use PHPCasts\Yaf\Log\Log;

/**
 * yaf 框架报错类调用
 * 默认错误会调用这个Controller 中 ErrorAction
 */
class ErrorController extends Web
{
    /**
     * @param Exception $exception
     */
    public function errorAction(Exception $exception)
    {
        // $exception = $this->getRequest()->getException();
        $this->_view->setScriptPath(APP_PATH . '/views');

        switch ($exception->getCode()) {
            case YAF\ERR\AUTOLOAD_FAILED:
            case YAF\ERR\NOTFOUND\MODULE:
            case YAF\ERR\NOTFOUND\CONTROLLER:
            case YAF\ERR\NOTFOUND\ACTION:
            case YAF\ERR\NOTFOUND\VIEW:
                if (strpos($this->getRequest()->getRequestUri(), '.css') !== false ||
                    strpos($this->getRequest()->getRequestUri(), '.jpg') !== false ||
                    strpos($this->getRequest()->getRequestUri(), '.js') !== false ||
                    strpos($this->getRequest()->getRequestUri(), '.png') !== false ||
                    strpos($this->getRequest()->getRequestUri(), '.ico') !== false ||
                    strpos($this->getRequest()->getRequestUri(), '.gif') !== false
                ) {
                    header('HTTP/1.1 404 Not Found');
                }
                $data = [
                    'type' => '404',
                    'message' => 'Not Found!',
                    'debug' => '',
                ];
                break;
            default:
                //记录错误日志
                Log::error(
                    $exception->getMessage() . ' IN FILE ' . $exception->getFile() . ' ON LINE ' . $exception->getLine(),
                    [$exception->getTraceAsString()]
                );

                $data = [
                    'type' => 'error',
                    'message' => '网络错误,请稍候再试！',
                    'debug' => '',
                ];

                if (
                    $exception instanceof \PHPCasts\Exceptions\ArgumentException
                    || $exception instanceof \PHPCasts\Exceptions\AuthException
                    || $exception instanceof \PHPCasts\Exceptions\HttpException
                ) {
                    $data['message'] = $exception->getMessage();
                }

                if (isset($this->config['application']['showErrors']) && $this->config['application']['showErrors']) {
                    $data['message'] = $exception->getMessage();
                    $data['debug'] = print_r($exception, true);
                }
        }
        $this->display('error', $data);
    }
}