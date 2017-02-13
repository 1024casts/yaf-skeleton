<?php
namespace Core\Queue;

use Core\Di\InjectionWareTrait;
use Core\LoggerAwareTrait;

class Worker
{
    use LoggerAwareTrait;
    use InjectionWareTrait;

    /**
     * @var Queue
     */
    protected $mq;

    protected $tries;

    public function __construct($mq, $tries = 3)
    {
        $this->mq = $mq;
        $this->tries = $tries;
    }

    public function loop()
    {
        // @todo 监听停止信号并正常处理(不接收新任务, 不中断执行现有任务)后再退出

        while (true) {
            $job = $this->mq->pop($this->mq->getQueue());
            if ($job === false) {
                continue;
            }

            $this->logger->debug('perform job', [$job]);

            $this->perform($job);
        }
    }

    /**
     * 执行任务
     *
     * @param array $data [
     *                  'id' => 'a random id which should be unique',
     *                  'timestamp' => 1468234243,
     *                  'tries' => 0,
     *                  'maxTries' => 10,  // 自定义最多失败多少次后停止执行
     *                  'delay' => 5,  // 初始任务延时多少秒执行
     *                  'job' => '\Some\Class@method or \Some\Class or service name in container',
     *                  'data' => ['k1' => 'v1' , 'k2' => 'v2', ...]
     *              ]
     *
     * @return bool
     */
    protected function perform($data) {
        if (!$data) {
            $this->logger->warning('empty job data, maybe something wrong');

            return false;
        }

        if ((isset($data['maxTries']) && $data['tries'] >= $data['maxTries'])
            || $data['tries'] >= $this->tries) {
            return $this->mq->confirm($data, Queue::CONFIRM_ERR);
        }
        
        if (strpos($data['job'], '@') !== false) {
            list($cls, $method) = explode('@', $data['job'], 2);
        } else {
            $cls = $data['job'];
            $method = 'perform';
        }

        try {
            $obj = $this->di->get($cls);

            if (!method_exists($obj, $method)) {
                return $this->mq->confirm($data, Queue::CONFIRM_ERR);
            }

            $rs = call_user_func([$obj, $method], $data['data']);
            if ($rs !== true) {
                return $this->mq->confirm($data, Queue::CONFIRM_FAIL);
            }

            return $this->mq->confirm($data, Queue::CONFIRM_SUCC);
        } catch (\Exception $e) {
            $this->logger->warning('perform job queue exception', ['code' => $e->getCode(), 'msg' => $e->getMessage(), 'trace' => $e->getTrace()]);

            return $this->mq->confirm($data, Queue::CONFIRM_ERR);
        }
    }
}