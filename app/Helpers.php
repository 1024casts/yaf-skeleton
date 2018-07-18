<?php

use Yaf\Registry;

if (!function_exists('app')) {
    /**
     * @param $make
     * @return \PHPCasts\Yaf\ServiceContainer
     */
    function app($make)
    {
        $container = Registry::get('container');

        if (!isset($container[$make])) {
            throw new InvalidArgumentException('not exist in container');
        }

        return $container[$make];
    }
}