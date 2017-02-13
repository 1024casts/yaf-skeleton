<?php
namespace Core\Di;

interface ContainerInterface
{
    function set($name, $service);
    function get($name);
}