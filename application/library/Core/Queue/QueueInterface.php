<?php
namespace Core\Queue;

interface QueueInterface
{
    /**
     * 推送任务到消息队列
     *
     * @param string $job
     * @param mixed $data
     * @param string $queue
     * @return bool
     */
    public function push($job, $data = null, $queue = null);

    /**
     * 从队列中"弹出"最近的任务
     * @attention 须实现"阻塞"处理
     *
     * @param string $queue
     *
     * @return mixed|bool 获取不到任务, 返回false
     */
    public function pop($queue = null);

    /**
     * 推送延时任务到消息队列
     * @todo move to Delay*?
     *
     * @param $delay
     * @param $job
     * @param null $data
     * @param null $queue
     * @return bool
     */
    public function delay($delay, $job, $data = null, $queue = null);

    /**
     * 确认处理
     *
     * @param array $payload
     * @param mixed $result
     * @param string $queue
     * @return bool
     */
    public function confirm($payload, $result, $queue = null);
}
