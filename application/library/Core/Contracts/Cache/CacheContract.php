<?php

namespace Core\Contracts\Cache;

/**
 * 缓存接口
 */
interface CacheContract
{
    /**
     * 创建连接
     *
     * @param $config
     */
    public function __construct($config);

    /**
     * 断开连接
     */
    public function __destruct();

    /**
     * 获取缓存
     *
     * @param string $key 缓存Key
     * @return mixed 不存在时返回null
     */
    public function get($key);

    /**
     * 批量读取缓存
     *
     * @param array $keys 要读取的缓存Key列表
     * @return array Key到值的Map
     */
    public function getMulti($keys);

    /**
     * 设置缓存
     *
     * @param string $key 缓存Key
     * @param mixed $val 缓存的值,自动编码
     * @param int $expire 有效期, 0为不过期
     * @return bool 成功返回true, 失败false
     */
    public function set($key, $val, $expire = 0);

    /**
     * 批量设置缓存
     *
     * @param array $data 要设置的缓存,键为缓存的Key
     * @param int $expire 有效期, 0为不过期
     * @return array Key到值的Map
     */
    public function setMulti($data, $expire = 0);

    /**
     * 设置缓存有效期
     *
     * @param string $key 缓存Key
     * @param int $expire 有效期, 0为不过期
     * @return bool 成功返回true, 失败false
     */
    public function setExpire($key, $expire = 0);

    /**
     * 设置缓存有效期到某个时间为止
     *
     * @param string $key 缓存Key
     * @param int $time 有效期,时间戳
     * @return bool 成功返回true, 失败false
     */
    public function expiresAt($key, $time = 0);

    /**
     * 删除缓存
     *
     * @param string $key 缓存Key
     * @return bool 成功返回true, 失败false
     */
    public function delete($key);

    /**
     * 递增某个Key,不存在则自动创建
     *
     * @param string $key 缓存Key
     * @param int $offset 增加的值
     * @return mixed
     */
    public function increment($key, $offset = 1);

    /**
     * 递减某个Key,不存在则自动创建
     *
     * @param string $key 缓存Key
     * @param int $offset 减少的值
     * @return mixed
     */
    public function decrement($key, $offset = 1);
}