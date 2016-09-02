<?php

namespace Core\Http;

use Yaf\Registry;

/**
 * Cookie
 *
 * 配置文件:
 * cookie.domain = example.com
 * cookie.expire = 0
 * cookie.httpOnly = true
 * --------------------------
 * 设置Cookie
 * Cookies::setExpire(1800)->set('hello', 'world');
 * 删除Cookie
 * Cookies::del('hello');
 */
class Cookies
{
    /**
     * 配置
     *
     * @var array
     */
    protected static $config;

    /**
     * @var Support\Cookies
     */
    protected static $instance;

    /**
     * 具体支持的方法参见Support\Cookies
     *
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public static function __callStatic($name, $arguments)
    {
        if (!static::$instance) {
            static::$instance = new Support\Cookies(static::getConfig());
        }

        $cookies = clone static::$instance;
        return call_user_func_array([$cookies, $name], $arguments);
    }

    /**
     * 获取配置
     *
     * @return array
     */
    public static function getConfig()
    {
        if (static::$config !== null) {
            return static::$config;
        }

        $configs = Registry::get('config');
        return static::$config = isset($configs['cookie']) ? $configs['cookie'] : [];
    }

    /**
     * 修改配置
     *
     * @param array $configs
     * @return bool
     */
    public static function setConfig(array $configs)
    {
        static::$instance = null;
        static::$config = $configs;
        return true;
    }
}