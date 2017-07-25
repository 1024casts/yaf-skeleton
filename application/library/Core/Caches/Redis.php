<?php

namespace Core\Caches;

/**
 * Redis Cache
 */
class Redis implements CacheInterface
{
    /**
     *声明redis对象
     *
     * @var \Redis
     */
    private $redis;

    /**
     * 创建连接
     *
     * @param $redisObj
     */
    public function __construct($redisObj)
    {
        $this->redis = $redisObj;
    }

    /**
     * 断开连接
     */
    public function __destruct()
    {
        $this->redis->close();
    }

    /**
     * 获取缓存
     *
     * @param string $key
     * @return mixed
     */
    public function get($key)
    {
        $ret = $this->redis->get($key);
        if (is_numeric($ret)) {
            return $ret;
        }

        return $ret === false ? false : json_decode($ret, true);
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

        $ret = $this->redis->mget($keys);
        foreach ($ret as & $v) {
            $v = json_decode($v, true);
        }

        return $ret;
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
        if (!is_numeric($val)) {
            $val = json_encode($val);
        }

        if ($expire) {
            return $this->redis->setex($key, $expire, $val);
        }

        return $this->redis->set($key, $val);
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

        foreach ($data as &$d) {
            if (!is_numeric($d)) {
                $d = json_encode($d);
            }
        }

        $retFlag = $this->redis->mset($data);
        if ($expire > 0) {
            foreach ($data as $key => $value) {
                $this->redis->expire($key, $expire);
            }
        }

        return $retFlag;
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
        return $this->redis->expire($key, $expire);
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
        return $this->redis->expire($key, $expire);
    }

    /**
     * 删除缓存
     *
     * @param string $key 缓存Key
     * @return bool 成功返回true, 失败false
     */
    public function delete($key)
    {
        return $this->redis->delete($key);
    }

    /**
     * @inheritdoc
     */
    public function deleteMulti($keys)
    {
        return call_user_func_array([$this->redis, 'delete'], $keys);
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
        return $this->redis->incrBy($key, $offset);
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
        return $this->redis->decrBy($key, $offset);
    }
}