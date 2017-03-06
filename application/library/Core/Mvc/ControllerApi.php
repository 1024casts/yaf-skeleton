<?php

namespace Core\Mvc;

use Core\Di\InjectionWareInterface;
use Core\Mvc\Controller\Api as CoreApi;
use Core\Di\InjectionWareTrait;
use Core\Events\ManagerWareTrait;

/**
 * 基本API控制器, 含DI和EventManager支持
 */
class ControllerApi extends CoreApi implements InjectionWareInterface
{
    use InjectionWareTrait;
    use ManagerWareTrait;

    // @attention \Yaf\Controller_Abstract源码(2.3.5)中声明了该宏变量但没有定义该属性, 会导致InjectionWareTrait::__get方法获取不到对应服务从而触发异常
    public $yafAutoRender = false;

    /**
     * 初始化
     * @attention 接口控制器, 直接设置json输出头; 不使用response
     */
    public function init()
    {
        parent::init();
        $this->autoInjectProperty();

        header('Content-Type: application/json');
    }

    protected function autoInjectProperty()
    {
        $ro = new \ReflectionObject($this);
        foreach ($ro->getProperties(\ReflectionProperty::IS_PRIVATE) as $property) {
            if ($property->class == static::class) {
                preg_match_all('#@(.*?)\n#s', $property->getDocComment(), $annotations);
                foreach ($annotations[1] as $annotation) {
                    preg_match('#(.*?)\s+(.+)#', trim($annotation), $match);
                    if (count($match) >= 3 && $match[1] == 'inject') {
                        $property->setAccessible(true);
                        $property->setValue($this, $this->di->get($match[2]));
                    }
                }
            }
        }
    }
}
