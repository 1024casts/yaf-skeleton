<?php
namespace Core\Di;

use Pimple\Container as PimpleContainer;

/**
 * Class Container
 */
class Container implements ContainerInterface, \ArrayAccess
{
    /**
     * @var PimpleContainer
     */
    protected $pimple;

    /**
     * @var Container
     */
    protected static $default;

    /**
     * Container constructor.
     *
     * @param array $values service name => class name|anonymous function 键值对
     */
    public function __construct($values = [])
    {
        $this->pimple = new PimpleContainer();

        foreach ($values as $k => $v) {
            $this->offsetSet($k, $v);
        }

        static::$default = $this;
    }

    public function __call($method, $params)
    {
        return call_user_func_array([$this->pimple, $method], $params);
    }

    public function offsetSet($offset, $value)
    {
        if (is_string($value)) {
            $cls = $value;
            if (!class_exists($cls)) {
                throw new \Exception('only support class name and anonymous function');
            }

            $value = function () use ($cls) {
                $obj = new $cls;
                if ($obj instanceof InjectionWareInterface) {
                    $obj->setDi($this);
                }

                return $obj;
            };
        } else {
            $old = $value;
            $value = function ($container) use ($old) {
                $obj = $old($container);
                if ($obj instanceof InjectionWareInterface) {
                    $obj->setDi($this);
                }

                return $obj;
            };
        }
        // @todo support more like: array, object

        $this->pimple[$offset] = $value;
    }

    public function set($name, $service)
    {
        $this->offsetSet($name, $service);
    }

    public function get($name)
    {
        if (!isset($this->pimple[$name])) {
            if (strpos($name, '.') !== false) {
                // @todo move out for decouple
                $defines = [
                    'srv' => ['prefix' => 'App\\Services\\', 'suffix' => ''],
                    'hlp' => ['prefix' => 'App\\Helpers\\', 'suffix' => ''],
                    'mdl' => ['prefix' => '', 'suffix' => 'Model'],
                ];

                if (isset($defines[$ns = substr($name, 0, 3)])) {
                    $cls = $defines[$ns]['prefix']
                        . str_replace(" ", "\\", ucwords(str_replace(".", " ", substr($name, 4))))
                        . $defines[$ns]['suffix'];

                    $this->offsetSet($name, $cls);
                }
            } elseif (class_exists($name)) {
                $this->offsetSet($name, $name);
            }
        }

        return $this->pimple[$name];
    }

    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    public function offsetExists($offset)
    {
        return isset($this->pimple[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->pimple[$offset]);
    }

    public function getRaw($name)
    {
        return $this->pimple->raw($name);
    }

    /**
     * 设置默认容器
     *
     * @param Container $di
     */
    public static function setDefault($di)
    {
        static::$default = $di;
    }

    /**
     * 返回最后设置的容器
     *
     * @return Container
     */
    public static function getDefault()
    {
        if (!static::$default) {
            return new static();
        }

        return static::$default;
    }

}
