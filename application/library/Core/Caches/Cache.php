<?php

namespace Core\Caches;

use Yaf\Registry;
use Core\Exceptions\ConfigException;
use Core\Database\Redis as RedisDb;

/**
 * 缓存类
 */
class Cache
{
    /**
     * @var CacheContract
     */
    protected static $instance;

    /**
     * 缓存实例
     */
    public static function getInstance()
    {
        if (self::$instance) {
            return self::$instance;
        }

        $config = Registry::get('config');
        if (!isset($config['cache']['type'])) {
            throw new ConfigException('No cache config!');
        }


        switch ($config['cache']['type']) {
            case 'redis' :
                $redisName = isset($config['cache']['redis']) ? $config['cache']['redis'] : 'default';
                self::$instance = new Redis(RedisDb::getInstance($redisName));
                break;

            case 'memcached' :
            default :
                self::$instance = new Memcached($config['memcached']);
        }


        return self::$instance;
    }
}