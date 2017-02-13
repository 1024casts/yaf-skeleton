<?php
namespace Core\Queue;

use Core\Di\InjectionWareTrait;
use Core\LoggerAwareTrait;

class Subscriber
{
    use LoggerAwareTrait;
    use InjectionWareTrait;

    /**
     * @var Queue
     */
    protected $mq;

    public function __construct($mq)
    {
        $this->mq = $mq;
    }

    public function start()
    {
        // @todo 监听停止信号并正常处理(不接收新任务, 不中断执行现有任务)后再退出

        while (true) {
            $event = $this->mq->pop($this->mq->getQueue());
            if ($event === false) {
                continue;
            }

            $this->logger->debug('event occurred', [$event]);

            $this->dispatch($event);
        }
    }

    /**
     * 分发事件
     *
     * @param array $event [
     *                  'id' => 'a random id which should be unique',
     *                  'timestamp' => 1468234243,
     *                  'type' => '\Some\Class or service name in container or someEntity:someEventOccurred',
     *                  'data' => ['k1' => 'v1' , 'k2' => 'v2', ...]
     *              ]
     *
     * @return bool
     */
    protected function dispatch($event) {
        if (empty($event['type'])) {
            $this->logger->warning('wrong event type, maybe something wrong');

            return false;
        }

        if (strpos($event['type'], ':') === false) {
            $event['type'] .= ':handleEvent';
        }

        try {
            $this->eventsManager->fire($event['type'], $this, $event['data']);
        } catch (\Exception $e) {
            $this->logger->warning('dispatch event exception while subscribing event queue', ['code' => $e->getCode(), 'msg' => $e->getMessage(), 'trace' => $e->getTrace()]);
        } finally {
            $this->mq->confirm($event, Queue::CONFIRM_SUCC);
        }

        return true;
    }
}