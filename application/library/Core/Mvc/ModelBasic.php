<?php

namespace Core\Mvc;

use Core\Di\InjectionWareInterface;
use Core\Di\InjectionWareTrait;
use Core\Caches\Memory;
use Core\Events\ManagerWareTrait;
use Core\Mvc\Model\CacheTrait;
use Core\Mvc\Model\DbTrait;
use Core\Mvc\Model\RedisTrait;

class ModelBasic implements InjectionWareInterface
{
    use InjectionWareTrait;
    use ManagerWareTrait;
    use DbTrait;
    use CacheTrait;
    use RedisTrait;

    /**
     * @var Memory
     */
    protected $localCache;

    public function __construct()
    {
        // @todo 更好的封装
        $this->localCache = new Memory();

        $this->init();
    }

    protected function init()
    {

    }
}
