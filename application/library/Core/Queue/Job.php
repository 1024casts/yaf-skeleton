<?php
namespace Core\Queue;

interface Job
{
    /**
     * 执行任务
     *
     * @param mixed $data 任务相关数据
     * @return bool|mixed 成功返回true, 否则返回其他
     */
    public function perform($data);
}
