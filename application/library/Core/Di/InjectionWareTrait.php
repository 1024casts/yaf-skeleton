<?php
namespace Core\Di;

use Core\Assert;
use Common\InnerService;
use Core\Events\Manager;
use Psr\Log\LoggerInterface;

/**
 * Class InjectionWareTrait
 *
 * @property Container $di
 * @property Assert $assert
 * @property Manager $eventsManager
 * @property LoggerInterface $logger
 * @property InnerService $innerSrv
 */
trait InjectionWareTrait
{
    /**
     * 设置依赖注入容器
     *
     * @param ContainerInterface $di
     */
    public function setDi($di)
    {
        $this->di = $di;
    }

    /**
     * 获取依赖注入容器
     *
     * @return ContainerInterface
     */
    public function getDi()
    {
        return isset($this->di) ? $this->di : Container::getDefault();
    }

    public function __get($name)
    {
        if ($name == 'di') {
            return $this->di = $this->getDi();
        }

        return $this->di[$name];
    }
}