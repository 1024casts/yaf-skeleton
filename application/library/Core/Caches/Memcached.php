<?php

namespace Core\Caches;

/**
 * Memcached Cache
 */
class Memcached implements CacheInterface
{
    /**
     * 声明memcache对象
     */
    private $mem;

    /**
     * 创建连接
     *
     * @param $config
     */
    public function __construct($config)
    {
        $this->mem = new \Memcached();
        $this->mem->addServers($config);
    }

    /**
     * 断开连接
     */
    public function __destruct()
    {
        $this->mem->quit();
    }

    /**
     * 获取缓存
     *
     * @param string $key
     * @return mixed
     */
    public function get($key)
    {
        return $this->mem->get($key);
    }

    /**
     * 批量读取缓存
     *
     * @param array $keys 要读取的缓存Key列表
     * @return array|bool Key到值的Map
     */
    public function getMulti($keys)
    {
        if (!is_array($keys)) {
            return false;
        }
        foreach ($keys as $n => $k){
            $keys[$n] = $this->mem->get($k);
        }
        return $this->mem->getMulti($keys);
    }

    /**
     * 设置缓存
     *
     * @param string $key 缓存Key
     * @param mixed $val 缓存的值,自动编码
     * @param int $expire 有效期, 0为不过期
     * @return bool 成功返回true, 失败false
     */
    public function set($key, $val, $expire = 0)
    {
        return $this->mem->set($key, $val, $expire);
    }

    /**
     * 批量设置缓存
     *
     * @param array $data 要设置的缓存,键为缓存的Key
     * @param int $expire 有效期, 0为不过期
     * @return array|bool Key到值的Map
     */
    public function setMulti($data, $expire = 0)
    {
        if (!is_array($data)) {
            return false;
        }
        return $this->setMulti($data, $expire);
    }

    /**
     * 设置缓存有效期
     *
     * @param string $key 缓存Key
     * @param int $expire 有效期, 0为不过期
     * @return bool 成功返回true, 失败false
     */
    public function setExpire($key, $expire = 0)
    {
        $val = $this->mem->get($key);
        if (isset($val)) {
            return $this->set($key, $val, $expire);
        }

        return false;
    }

    /**
     * 设置缓存有效期到某个时间为止
     *
     * @param string $key 缓存Key
     * @param int $time 有效期,时间戳
     * @return bool 成功返回true, 失败false
     */
    public function expiresAt($key, $time = 0)
    {
        $expire = $time - time();
        return $this->setExpire($key,$expire);
    }

    /**
     * 删除缓存
     *
     * @param string $key 缓存Key
     * @return bool 成功返回true, 失败false
     */
    public function delete($key)
    {
        return $this->mem->delete($key);
    }

    /**
     * @inheritdoc
     */
    public function deleteMulti($keys)
    {
        return $this->mem->deleteMulti($keys);
    }

    /**
     * 递增某个Key,不存在则自动创建
     *
     * @param string $key 缓存Key
     * @param int $offset 增加的值
     * @return mixed
     */

    public function increment($key, $offset = 1)
    {
        return $this->mem->increment($key, $offset);
    }

    /**
     * 递减某个Key,不存在则自动创建
     *
     * @param string $key 缓存Key
     * @param int $offset 减少的值
     * @return mixed
     */
    public function decrement($key, $offset = 1)
    {
        return $this->mem->decrement($key, $offset);
    }
}