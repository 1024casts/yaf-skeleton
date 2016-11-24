<?php

namespace Core\Databases;

use Yaf\Registry;
use Core\Exceptions\ConfigException;
use RedisException;
use Redis;

/**
 * Redis数据库
 */
class RedisDb extends Redis
{
    /**
     * Redis对象实例
     *
     * @var array
     */
    protected static $instance = [];

    /**
     * 获取Redis链接实例
     *
     * @param string $name
     * @throws ConfigException|RedisException
     * @return self
     */
    public static function getInstance($name = 'default')
    {
        if (isset(self::$instance[$name])) {
            return self::$instance[$name];
        }

        $config = Registry::get('config');
        $config = $config['redis'];
        if (!isset($config[$name], $config[$name]['host'])) {
            throw new ConfigException('No redis config!');
        }

        $config = $config[$name];
        $connectType = isset($config['pconnect']) && $config['pconnect'] ? 'pconnect' : 'connect';

        $redis = new self;
        if (!$redis->$connectType(
            $config['host'],
            isset($config['port']) ? $config['port'] : 6379,
            isset($config['timeout']) ? $config['timeout'] : 1
        )) {
            throw new RedisException('Redis server went away!');
        }

        if (isset($config['prefix'])) {
            $redis->setOption(Redis::OPT_PREFIX, $config['prefix']);
        }

        return self::$instance[$name] = $redis;
    }
}