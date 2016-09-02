<?php

namespace Core\Http\Support;

/**
 * Cookie操作
 *
 * $config = ['domain' => 'example.com', 'prefix' => 'eg_'];
 * (new Cookie($config))->set('hello', 'world');
 * (new Cookie($config))->del('hello');
 */
class Cookies
{
    /**
     * Cookie 域
     *
     * @var string
     */
    protected $domain = '';

    /**
     * Cookie 前缀
     *
     * @var string
     */
    protected $prefix = '';

    /**
     * Cookie 路径
     *
     * @var string
     */
    protected $path = '/';

    /**
     * Cookie 有效期
     *
     * @var int
     */
    protected $expire = 0;

    /**
     * Cookie 是否只在HTTPS使用
     *
     * @var bool
     */
    protected $secure = false;

    /**
     * Cookie 是否只在HTTPS使用
     *
     * @var bool
     */
    protected $httpOnly = true;

    /**
     * Cookies constructor.
     *
     * @param array $configs
     */
    public function __construct(array $configs)
    {
        foreach ($configs as $option => $val) {
            if (property_exists($this, $option)) {
                $this->$option = $val;
            }
        }
    }

    /**
     * 设置前缀
     *
     * @param string $prefix
     * @return $this
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
        return $this;
    }

    /**
     * 设置过期时间
     *
     * @param int $expire
     * @return $this
     */
    public function setExpire($expire)
    {
        // 一年内认为是相对时间
        if ($expire < 31536001 && $expire != 0) {
            $expire += time();
        }

        $this->expire = $expire;
        return $this;
    }

    /**
     * 设置域
     *
     * @param string $domain
     * @return $this
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
        return $this;
    }

    /**
     * 设置是否只通过HTTP传递
     *
     * @param bool $isHttpOnly
     * @return $this
     */
    public function setHttpOnly($isHttpOnly = true)
    {
        $this->httpOnly = $isHttpOnly;
        return $this;
    }

    /**
     * 获取Cookie值
     *
     * @param string $name
     * @param mixed $default
     * @return string|null
     */
    public function get($name, $default = null)
    {
        $name = $this->prefix . $name;
        return isset($_COOKIE[$name]) ? $_COOKIE[$name] : $default;
    }

    /**
     * 设置Cookie
     *
     * @param string $name
     * @param string $value
     * @return bool
     */
    public function set($name, $value)
    {
        $name = $this->prefix . $name;
        return setcookie($name, $value, $this->expire, $this->path, $this->domain, $this->secure, $this->httpOnly);
    }

    /**
     * 删除Cookie
     *
     * @param $name
     * @return bool
     */
    public function del($name)
    {
        $name = $this->prefix . $name;
        return setcookie($name, '', 1, $this->path, $this->domain, $this->secure, $this->httpOnly);
    }
}