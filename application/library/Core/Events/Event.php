<?php
namespace Core\Events;

class Event
{
    /**
     * 事件类型
     * @var string
     */
    protected $type;

    /**
     * 事件来源对象
     *
     * @var object
     */
    protected $source;

    /**
     * 事件数据
     * @var mixed
     */
    protected $data;

    /**
     * 事件是否停止传播
     * @var bool
     */
    protected $stopped = false;

    /**
     * 事件是否可取消(停止传播)
     *
     * @var bool
     */
    protected $cancelable = true;

    /**
     * 构造方法
     *
     * @param string $type
     * @param object $source
     * @param mixed  $data
     * @param bool   $cancelable
     */
    public function __construct($type, $source, $data = null, $cancelable = true)
    {
        $this->type = $type;
        $this->source = $source;

        if ($data !== null) {
            $this->data = $data;
        }

        if ($cancelable !== true) {
            $this->cancelable = $cancelable;
        }
    }

    /**
     * 设置事件类型
     *
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * 获取事件类型
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * 获取事件来源对象
     *
     * @return object
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * 设置事件数据
     *
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * 获取事件数据
     *
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * 事件是否可取消
     *
     * @return bool
     */
    public function getCancelable()
    {
        return $this->cancelable;
    }

    /**
     * 停止事件, 防止(往后)传播
     *
     * @throws \Exception
     */
    public function stop()
    {
        if (!$this->cancelable) {
            throw new \Exception('事件不能停止');
        }

        $this->stopped = true;
    }

    /**
     * 事件是否已停止(传播)
     *
     * @return bool
     */
    public function isStopped()
    {
        return $this->stopped;
    }
}
