<?php
namespace Core\Events;

use SplPriorityQueue as PriorityQueue;

/**
 * 事件管理类
 */
class Manager
{
    /**
     * 事件监听者数组
     *
     * @var array
     */
    protected $listeners = [];

    /**
     * 是否启用优先级顺序处理
     *
     * @var bool
     */
    protected $enablePriorities = false;

    /**
     * 设置是否启用优先级顺序
     *
     * @param bool $enablePriorities
     */
    public function enablePriorities($enablePriorities)
    {
        $this->enablePriorities = $enablePriorities;
    }

    /**
     * 是否启用优先级顺序
     *
     * @return bool
     */
    public function arePrioritiesEnabled()
    {
        return $this->enablePriorities;
    }

    /**
     * 添加事件监听者
     *
     * @param string $eventType 事件类型
     * @param object|callable|string $handler 事件处理对象或回调对象或类名
     * @param int $priority 优先级, 数值越大优先级越高
     *
     * @throws \Exception
     */
    public function attach($eventType, $handler, $priority = 100)
    {
        if (!isset($this->listeners[$eventType])) {
            if ($this->enablePriorities) {
                $priorityQueue = new PriorityQueue();
                $priorityQueue->setExtractFlags(PriorityQueue::EXTR_DATA);
            } else {
                $priorityQueue = [];
            }

            $this->listeners[$eventType] = $priorityQueue;
        }

        if (is_object($this->listeners[$eventType])) {
            /** @var PriorityQueue $priorityQueue */
            $priorityQueue = &$this->listeners[$eventType];
            $priorityQueue->insert($handler, $priority);
        } else {
            $this->listeners[$eventType][] = $handler;
        }
    }

    /**
     * 移除事件监听者
     *
     * @param string $eventType
     * @param object|callable|string $handler
     *
     * @throws \Exception
     */
    public function detach($eventType, $handler)
    {
        if (!isset($this->listeners[$eventType])) {
            return;
        }

        if (is_object($this->listeners[$eventType])) {
            // @attention PriorityQueue不支持移除
            $newPriorityQueue = new PriorityQueue();
            $newPriorityQueue->setExtractFlags(PriorityQueue::EXTR_DATA);

            /** @var PriorityQueue $oldPriorityQueue */
            $oldPriorityQueue = &$this->listeners[$eventType];
            $oldPriorityQueue->setExtractFlags(PriorityQueue::EXTR_BOTH);
            $oldPriorityQueue->top();

            foreach ($oldPriorityQueue as $currHandler) {
                if ($currHandler['data'] !== $handler) {
                    $newPriorityQueue->insert($currHandler['data'], $currHandler['priority']);
                }
            }

            $this->listeners[$eventType] = $newPriorityQueue;
        } else {
            if (($idx = array_search($handler, $this->listeners[$eventType], true)) !== false) {
                unset($this->listeners[$eventType][$idx]);
            }
        }
    }

    public function addSubscriber(SubscriberInterface $subscriber)
    {
        foreach ($subscriber->getSubscribedEvents() as $eventType => $params) {
            if (is_string($params)) {
                $this->attach($eventType, [$subscriber, $params]);
            } elseif (is_string($params[0])) {
                $this->attach($eventType, [$subscriber, $params[0]], isset($params[1]) ? $params[1] : 100);
            } else {
                foreach ($params as $listener) {
                    $this->attach($eventType, [$subscriber, $listener[0]], isset($listener[1]) ? $listener[1] : 100);
                }
            }
        }
    }

    public function removeSubscriber(SubscriberInterface $subscriber)
    {
        foreach ($subscriber->getSubscribedEvents() as $eventName => $params) {
            if (is_array($params) && is_array($params[0])) {
                foreach ($params as $listener) {
                    $this->detach($eventName, [$subscriber, $listener[0]]);
                }
            } else {
                $this->detach($eventName, [$subscriber, is_string($params) ? $params : $params[0]]);
            }
        }
    }

    /**
     * 移除对应事件所有监听者
     *
     * @param string $type
     */
    public function detachAll($type = null)
    {
        if ($type === null) {
            $this->listeners = [];
        } else {
            if (isset($this->listeners[$type])) {
                unset($this->listeners[$type]);
            }
        }
    }

    /**
     * 触发事件, 通知相关监听者执行响应处理
     *
     * @param string $eventType 触发的事件类型, 必须包括":", 如"db:beforeQuery"
     * @param object $source 事件来源对象
     * @param mixed $data 附加的事件数据
     * @param bool $cancelable 是否可取消, 停止往后传播
     *
     * @throws \Exception
     */
    public function fire($eventType, $source, $data = null, $cancelable = true)
    {
        if (!$this->listeners) {
            return;
        }

        if (strpos($eventType, ':') === false) {
            throw new \Exception('事件类型字符串必须使用":"分隔');
        }

        list($type, $eventName) = explode(':', $eventType);

        if (!isset($this->listeners[$type]) && !isset($this->listeners[$eventType])) {
            return;
        }

        $event = new Event($eventName, $source, $data, $cancelable);

        // 总类型(如"db")对应的事件处理
        if (isset($this->listeners[$type])) {
            $this->performEventHandlers($this->listeners[$type], $event);
        }

        // 分类型(如"db:beforeQuery")对应的事件处理
        if (isset($this->listeners[$eventType])) {
            $this->performEventHandlers($this->listeners[$eventType], $event);
        }
    }

    /**
     * 执行事件响应处理
     *
     * @param PriorityQueue|array $handlers
     * @param Event $event
     */
    private function performEventHandlers($handlers, $event)
    {
        if (is_object($handlers)) {
            // clone并重置到起始位置, 防止影响其他触发响应处理
            /** @var PriorityQueue $handlers */
            $handlers = clone $handlers;
            $handlers->top();
        }

        $cancelable = $event->getCancelable();

        foreach ($handlers as $handler) {
            $this->performEventHandler($event, $handler);

            if ($cancelable && $event->isStopped()) {
                break;
            }
        }
    }

    private function performEventHandler(Event $event, $handler)
    {
        $params = [$event, $event->getSource(), $event->getData()];

        switch (true) {
            case $handler instanceof \Closure:  // 匿名函数
                return call_user_func_array($handler, $params);
                break;

            case is_object($handler) && method_exists($handler, $event->getType()):   // 对象
                return call_user_func_array([$handler, $event->getType()], $params);
                break;
            case is_object($handler) && method_exists($handler, 'handle'.$event->getType()):
                return call_user_func_array([$handler, 'handle'.$event->getType()], $params);
                break;

            case is_array($handler) && is_string($handler[0]):    // like as [$cls, $method]
                if (!class_exists($handler[0])) {
                    throw new \Exception("class '{$handler[0]}' not exists");
                }

                return call_user_func_array([new $handler[0], $handler[1]], $params);
                break;

            case is_callable($handler): // like as [$obj, $method] or function name
                return call_user_func_array($handler, $params);
                break;

            case is_string($handler):   // class name
                if (!class_exists($handler)) {
                    throw new \Exception("class '{$handler}' not exists");
                }

                $handler = new $handler;
                $method = $event->getType();
                if (!method_exists($handler, $method)) {
                    $method = 'handle'.$event->getType();

                    if (!method_exists($handler, $method)) {
                        return null;
                    }
                }

                return call_user_func_array([$handler, $method], $params);
                break;

            default:
                // @todo log warning, 不应该执行到这个分支

                return null;
        }
    }

    /**
     * 返回事件的监听者
     *
     * @param string $eventType
     *
     * @return array
     */
    public function getListeners($eventType)
    {
        return isset($this->listeners[$eventType]) ? $this->listeners[$eventType] : [];
    }
}