<?php

namespace Core;

use Yaf\Dispatcher;

/**
 * Class Bootstrap
 */
class Bootstrap
{
    /**
     * Boot 依次调用类里面的_init开头的方法
     *
     * @param $className
     * @return bool
     */
    public static function boot($className)
    {
        if (!class_exists($className)) {
            return false;
        }

        $bootstrap = new $className;
        $dispatcher = Dispatcher::getInstance();

        $class = new \ReflectionClass($className);
        foreach ($class->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            $methodName = $method->name;
            if (!$method->isStatic() && substr_compare('_init', $methodName, 0, 5) === 0) {
                $bootstrap->$methodName($dispatcher);
            }
        }

        return true;
    }
}