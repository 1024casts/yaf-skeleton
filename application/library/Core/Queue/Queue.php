<?php
namespace Core\Queue;

use Core\Helpers\Str;
use Core\LoggerAwareTrait;

abstract class Queue implements QueueInterface
{
    use LoggerAwareTrait;

    // 确认
    const CONFIRM_SUCC = 1;
    const CONFIRM_FAIL = 2;
    const CONFIRM_ERR = 3;

    /**
     * 默认queue
     *
     * @var string
     */
    protected $queue;

    /**
     * 失败job延迟多长时间处理
     *
     * @var int
     */
    protected $failedJobDelaySecond = 5;

    public function setQueue($queue)
    {
        $this->queue = $queue;

        return $this;
    }

    public function getQueue($queue = null)
    {
        return $queue ?: $this->queue;
    }

    protected function createPayload($job, $data)
    {
        return [
            'id' => Str::random(32),    // longer string just for uniq job id
            'timestamp'=> time(),
            'tries' => 0,
            'delay' => 0,
            'job' => $job,
            'data' => $data,
        ];
    }

    protected function getDataQueue($queue, $payload)
    {
        // @attention 暂定分散到100个数据集中
        // @todo 调整改进?
        return $queue . ':datas:'.(crc32($payload['id']) % 100);
    }
}
