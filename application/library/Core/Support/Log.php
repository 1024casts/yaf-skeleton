<?php

namespace Core\Support;

use Yaf\Registry;
use Monolog\Logger;
use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogUdpHandler;
use Monolog\Formatter\LineFormatter;

class Log
{
    /**
     * Logger instance.
     *
     * @var \Psr\Log\LoggerInterface
     */
    protected static $logger;

    protected static $channelName = 'yaf.admin';

    /**
     * Return the logger instance.
     *
     * @return \Psr\Log\LoggerInterface
     */
    public static function getLogger()
    {
        return self::$logger[self::$channelName] ?: self::$logger[self::$channelName] = self::createDefaultLogger();
    }

    /**
     * Set logger.
     *
     * @param LoggerInterface|\Psr\Log\LoggerInterface $logger
     */
    public static function setLogger(LoggerInterface $logger)
    {
        self::$logger[self::$channelName] = $logger;
    }

    /**
     * Tests if logger exists.
     *
     * @return bool
     */
    public static function hasLogger()
    {
        return self::$logger[self::$channelName] ? true : false;
    }

    /**
     * Forward call.
     *
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     */
    public static function __callStatic($method, $args)
    {
        return forward_static_call_array([self::getLogger(), $method], $args);
    }

    /**
     * Make a default log instance.
     *
     * @return Logger
     */
    private static function createDefaultLogger()
    {
        $config = Registry::get('config');
        $logger = new Logger($config['log']['channel_name'] ?: self::$channelName);

        if (!$config['application']['debug'] || defined('PHPUNIT_RUNNING')) {
            $logger->pushHandler(new NullHandler());
        } elseif ($logFile = $config['log']['directory']) {
            switch ($config['log']['type']) {
                case 'syslog' :
                    $requestId = self::getRequestId();
                    $handler = new SyslogUdpHandler(
                        $config['host'],
                        $config['port'],
                        $facility = LOG_USER,
                        $level = 'debug'
                    );
                    $handler->setFormatter(
                        new LineFormatter(
                            '[%datetime%] | %channel% | ' . $requestId . ' | %level_name% | %message% | %context%'
                        )
                    );
                    break;
                case 'file':
                default:
                    $handler = new StreamHandler($logFile . date('Y-m-d') . '.log', $config['log']['level']);
            }
            $logger->pushHandler($handler);
        }

        return $logger;
    }

    /**
     * Get Request ID
     *
     * @return string
     */
    private static function getRequestId()
    {
        return isset($_SERVER['HTTP_X_REQUEST_ID']) ? $_SERVER['HTTP_X_REQUEST_ID'] : '-';
    }
}