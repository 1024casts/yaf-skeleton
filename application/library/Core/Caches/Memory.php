<?php

namespace Core\Caches;

/**
 * Class Memory
 * 当前请求内缓存数据
 */
class Memory implements CacheInterface, \ArrayAccess
{
    private $data = [];

    public function set($key, $val, $expire = 0)
    {
        $this->data[$key] = $val;
    }

    public function get($key)
    {
        return isset($this->data[$key]) ? $this->data[$key] : false;
    }

    public function setMulti($data, $expire = 0)
    {
        $this->data = array_merge($this->data, $data);
    }

    public function getMulti($keys)
    {
        return array_intersect_key($this->data, array_flip((array)$keys));
    }

    public function delete($key)
    {
        if (isset($this->data[$key])) {
            unset($this->data[$key]);
        }

        return true;
    }

    public function deleteMulti($keys)
    {
        $this->data = array_diff_key($this->data, array_flip((array)$keys));

        return true;
    }

    public function setExpire($key, $expire = 0)
    {
        return false;
    }

    public function expiresAt($key, $time = 0)
    {
        return false;
    }

    public function increment($key, $offset = 1)
    {
        if (!isset($this->data[$key])) {
            $this->data[$key] = 0;
        }

        $this->data[$key] += $offset;
    }

    public function decrement($key, $offset = 1)
    {
        if (!isset($this->data[$key])) {
            $this->data[$key] = 0;
        }

        $this->data[$key] -= $offset;
    }

    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    public function offsetUnset($offset)
    {
        $this->delete($offset);
    }

    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    public function offsetSet($offset, $value)
    {
        return $this->set($offset, $value);
    }

}
