<?php
namespace Core\Queue;

use Core\Helpers\Str;

class Event extends Queue
{
    /**
     * @var \Redis
     */
    protected $redis;

    /**
     * pop阻塞超时时间
     *
     * @attention 0 <= $timeout <= redis timeout; 若不在该区间内, 超时时redis会抛出连接错误异常
     * @attention 如果$timeout=0, 则redis timeout也必须为0, 均表示不触发超时处理
     *
     * @var int
     */
    protected $timeout = 5;

    public function __construct($redis, $queue = 'queues:default', $options = [])
    {
        $this->redis = $redis;
        $this->queue = $queue;

        foreach ($options as $k => $v) {
            if (property_exists($this, $k)) {
                $this->{$k} = $v;
            }
        }
    }

    /**
     * @param string $type
     * @param array|null $data
     * @param string|null $queue
     *
     * @return bool|mixed
     */
    public function dispatch($type, $data = null, $queue = null)
    {
        return $this->push($type, $data, $queue);
    }

    /**
     * @inheritdoc
     */
    public function push($type, $data = null, $queue = null)
    {
        $queue = $this->getQueue($queue);
        $payload = $this->createPayload($type, $data);
        $dataQueue = $this->getDataQueue($queue, $payload);

        $this->redis->multi();
        $this->redis->lPush($queue, $payload['id']);
        $this->redis->hSet($dataQueue, $payload['id'], json_encode($payload, JSON_UNESCAPED_UNICODE));
        $this->redis->exec();

        return $payload['id'];
    }

    protected function createPayload($type, $data)
    {
        return [
            'id' => Str::random(32),    // longer string just for uniq job id
            'timestamp'=> time(),
            'type' => $type,
            'data' => $data,
        ];
    }

    /**
     * @inheritdoc
     */
    public function pop($queue = null)
    {
        $queue = $this->getQueue($queue);

        while (true) {
            $jobId = $this->redis->brpoplpush($queue, $queue . ':dispatching', $this->timeout);
            if (!$jobId) {
                continue;
            }

            $this->logger->debug('pop job', ['jobId' => $jobId]);

            $dataQueue = $queue . ':datas:'.(crc32($jobId) % 100);
            $eventData = json_decode($this->redis->hGet($dataQueue, $jobId), true);

            if (!$eventData) {
                $this->logger->warning('event data not exists or wrong decode', ['jobId' => $jobId, 'eventData' => $eventData, 'code' => json_last_error(), 'msg' => json_last_error_msg()]);
                $this->redis->lRem($queue . ':dispatching', $jobId, -1);
            }

            return $eventData ?: false;
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function delay($delay, $job, $data = null, $queue = null)
    {
        throw new \Exception('not support');
    }

    /**
     * @inheritdoc
     */
    public function confirm($payload, $result, $queue = null)
    {
        $queue = $this->getQueue($queue);
        $dataQueue = $this->getDataQueue($queue, $payload);

        // @todo 统计计数等

        $this->redis->multi();
        $this->redis->lRem($queue.':dispatching', $payload['id'], -1);

        switch ($result) {
            case Queue::CONFIRM_SUCC:
                $this->redis->hDel($dataQueue, $payload['id']);
                break;

            default:
                trigger_error("abnormal queue event perform result [{$payload['id']}, {$result}, {$queue}]", E_USER_WARNING);

                return false;
                break;
        }
        $this->redis->exec();

        return true;
    }
}