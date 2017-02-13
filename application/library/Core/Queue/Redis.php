<?php
namespace Core\Queue;

class Redis extends Queue
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
     * @inheritdoc
     */
    public function push($job, $data = null, $queue = null)
    {
        $queue = $this->getQueue($queue);
        $payload = $this->createPayload($job, $data);
        $dataQueue = $this->getDataQueue($queue, $payload);

        $this->redis->multi();
        $this->redis->lPush($queue, $payload['id']);
        $this->redis->hSet($dataQueue, $payload['id'], json_encode($payload, JSON_UNESCAPED_UNICODE));
        $this->redis->exec();

        return $payload['id'];
    }

    /**
     * @inheritdoc
     */
    public function pop($queue = null)
    {
        $queue = $this->getQueue($queue);

        while (true) {
            $this->migrateDelayJobs($queue, ':failed');
            $this->migrateDelayJobs($queue, ':delay');

            $jobId = $this->redis->brpoplpush($queue, $queue . ':performing', $this->timeout);
            if (!$jobId) {
                continue;
            }

            $this->logger->debug('pop job', ['jobId' => $jobId]);

            $dataQueue = $queue . ':datas:'.(crc32($jobId) % 100);
            $jobData = json_decode($this->redis->hGet($dataQueue, $jobId), true);

            if (!$jobData) {
                $this->logger->warning('job data not exists or wrong decode', ['jobId' => $jobId, 'jobData' => $jobData, 'code' => json_last_error(), 'msg' => json_last_error_msg()]);
                $this->redis->lRem($queue . ':performing', $jobId, -1);
            }

            return $jobData ?: false;
        }

        return false;
    }

    protected function migrateDelayJobs($queue, $type) {
        // @todo locker implements in other library
        // @attention locker并发处理, 取不到则不做任何处理直接返回
        $this->redis->expire($queue.$type.':locker', max($this->timeout/2, 1));
        if ($this->redis->setnx($queue.$type.':locker', 1) === false) {
            return ;
        }

        $jobIds = $this->redis->zRangeByScore($queue.$type, '-inf', time());

        if (!$jobIds) {
            $this->redis->del($queue.$type.':locker');

            return ;
        }

        // @todo may be performance problem!
        $this->redis->multi();
        foreach ($jobIds as $id) {
            $this->logger->debug('migrate job', ['jobId' => $id, 'type' => $type]);

            if ($this->redis->lPush($queue, $id) !== false) {
                $this->redis->zRem($queue.$type, $id);
            }
        }
        $this->redis->exec();

        $this->redis->del($queue.$type.':locker');
    }

    /**
     * @inheritdoc
     */
    public function delay($delay, $job, $data = null, $queue = null)
    {
        $queue = $this->getQueue($queue);
        $payload = $this->createPayload($job, $data);
        $dataQueue = $this->getDataQueue($queue, $payload);

        $payload['delay'] = $delay;

        $this->redis->multi();
        $this->redis->zAdd($queue . ':delay', time()+$delay, $payload['id']);
        $this->redis->hSet($dataQueue, $payload['id'], json_encode($payload, JSON_UNESCAPED_UNICODE));
        $this->redis->exec();

        return $payload['id'];
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
        $this->redis->lRem($queue.':performing', $payload['id'], -1);

        switch ($result) {
            case Queue::CONFIRM_SUCC:
                $this->redis->hDel($dataQueue, $payload['id']);
                break;

            case Queue::CONFIRM_FAIL:
                $payload['tries'] += 1;
                $this->redis->hSet($dataQueue, $payload['id'], json_encode($payload, JSON_UNESCAPED_UNICODE));
                $this->redis->zAdd($queue.':failed', time()+$this->failedJobDelaySecond, $payload['id']);
                break;

            case Queue::CONFIRM_ERR:
                // @attention 仅记录, 暂不做处理
                // @todo 更好的处理方式
                $this->redis->zAdd($queue.':error', time(), $payload['id']);
                break;

            default:
                trigger_error("abnormal queue job perform result [{$payload['id']}, {$result}, {$queue}]", E_USER_WARNING);

                return false;
                break;
        }
        $this->redis->exec();

        // @todo adjust
        return true;
    }
}
