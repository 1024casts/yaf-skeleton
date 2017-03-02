<?php

namespace Core\Log;

use Psr\Log\LoggerInterface;
use Yaf\Registry;
use Monolog\Logger;
use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogUdpHandler;
use Monolog\Formatter\LineFormatter;
use Core\Exceptions\ConfigException;

/**
 * 通用日志类
 *
 * Example:
 *   调试日志, 生产勿开
 *   Log::debug('请求XXX, 返回结果是XXX');
 *   Log::debug('请求XXX, 返回结果是XXX', ['url' => 'http://xxx']);
 *
 *   信息类日志, 仅作为备注用
 *   Log::info('XXX登陆了后台');
 *   Log::info('XXX登陆了后台', ['ip' => '21.11.91.1', 'ua' => 'xxxx']);
 *
 *   提示类日志, 可能需要关注的日志
 *   Log::notice('接口传递了一个错误的参数');
 *
 *   警告类日志, 发生一个不严重的错误
 *   Log::warning('XXX重试了X次验证码');
 *   Log::warning('短信发送失败', ['phone' => '13xxxx', 'response' => '{"code":0, "msg":"xxx"}']);
 *
 *   错误类日志, 严重的错误, 程序发生错误
 *   Log::error('接口XXX超时了', ['url' => 'xxx']);
 *
 * @package Core
 */
class Log
{
    /**
     * 日志级别
     *
     * @var array
     */
    protected static $logLevels = [
        'debug'   => Logger::DEBUG,
        'info'    => Logger::INFO,
        'notice'  => Logger::NOTICE,
        'warning' => Logger::WARNING,
        'error'   => Logger::ERROR,
    ];

    /**
     * Logger instance.
     *
     * @var LoggerInterface
     */
    protected static $logger;

    /**
     * 默认频道
     *
     * @var string
     */
    protected static $defaultChannel = 'default';

    /**
     * 日志格式
     *
     * @var string
     */
    protected static $format = '[%datetime%] | %channel% | %request_id% | %level_name% | %message% | %context%';

    /**
     * 配置文件中的日志配置
     *
     * @var array
     */
    protected static $config;

    /**
     * 非常严重的错误
     *
     * @param string $message 错误的简单描述
     * @param array $context 错误的相关信息, 必须传入
     * @return bool
     */
    public static function error($message, array $context)
    {
        return static::log('error', $message, $context);
    }

    /**
     * 警告级别的日志
     *
     * @param string $message
     * @param array $context
     * @return bool
     */
    public static function warning($message, array $context = [])
    {
        return static::log('warning', $message, $context);
    }

    /**
     * 普通日志,但需要被注意的
     *
     * @param string $message
     * @param array $context
     * @return bool
     */
    public static function notice($message, array $context = [])
    {
        return static::log('notice', $message, $context);
    }

    /**
     * 可能感兴趣的日志
     *
     * Example: 谁登陆进来了, 数据库SQL日志等
     *
     * @param string $message
     * @param array $context
     * @return bool
     */
    public static function info($message, array $context = [])
    {
        return static::log('info', $message, $context);
    }

    /**
     * 调试用的日志
     *
     * @param string $message
     * @param array $context
     * @return bool
     */
    public static function debug($message, array $context = [])
    {
        return static::log('debug', $message, $context);
    }

    public static function log($level, $message, $context = [])
    {
        return static::getLogger()->log(static::$logLevels[$level], $message, $context);
    }

    /**
     * Make a default log instance.
     *
     * @return Logger|LoggerInterface
     * @throws ConfigException
     */
    protected static function getLogger()
    {
        static $day;

        if (static::$logger && $day == date('Y-m-d')) {
            return static::$logger;
        }

        $day = date('Y-m-d');

        $config = static::getConfig();
        $logger = new Logger($config['channel']);

        // 单元测试时不执行
        if (defined('PHPUNIT_RUNNING')) {
            $logger->pushHandler(new NullHandler());
        } else {
            // Syslog Log Handler
            if (isset($config['syslog']) && isset($config['syslog']['host'], $config['syslog']['port'])) {
                $handler = new SyslogUdpHandler($config['syslog']['host'], $config['syslog']['port'], LOG_USER, $config['level']);
                $handler->setFormatter(new LineFormatter(static::getFormat()));
                $logger->pushHandler($handler);
            }

            // File Log Handler
            if (isset($config['file'])) {
                // @todo bad APP_ROOT
                $logDir = isset($config['file']['dir']) ? $config['file']['dir'] : APP_ROOT . '/storage/logs';
                $logFile = $logDir . '/' . $day . '.log';

                $handler = new StreamHandler($logFile, $config['level'], true, 0777);
                $handler->setFormatter(new LineFormatter(static::getFormat() . "\n"));
                $logger->pushHandler($handler);
            }
        }

        return static::$logger = $logger;
    }

    /**
     * 获取配置信息
     *
     * @todo should be inject
     *
     * @return array
     * @throws ConfigException
     */
    protected static function getConfig()
    {
        if (static::$config) {
            return static::$config;
        }

        $config = Registry::get('config');
        if (!isset($config['log'])) {
            throw new ConfigException('log config not exists');
        }
        $config = $config['log'];

        if (!isset($config['level'])) {
            $config['level'] = 'info';
        }
        $config['level'] = isset(static::$logLevels[$config['level']])
            ? static::$logLevels[$config['level']]
            : Logger::INFO;

        if (!isset($config['channel'])) {
            $config['channel'] = static::$defaultChannel;
        }

        return static::$config = $config;
    }

    /**
     * 获取日志格式
     *
     * @return mixed
     */
    protected static function getFormat()
    {
        return strtr(static::$format, ['%request_id%' => static::getRequestId()]);
    }

    /**
     * Get Request ID
     *
     * @return string
     */
    protected static function getRequestId()
    {
        return isset($_SERVER['HTTP_X_REQUEST_ID']) ? $_SERVER['HTTP_X_REQUEST_ID'] : '-';
    }
}