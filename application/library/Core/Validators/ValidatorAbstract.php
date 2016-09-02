<?php

namespace Core\Validators;

/**
 * 验证器
 *
 * 为避免多次IO,暂时在一个里面,以后可以考虑拆开
 */
abstract class ValidatorAbstract
{
    /**
     * 字段名称
     *
     * @var string
     */
    protected $name;

    /**
     * 提示内容
     *
     * @var string
     */
    protected $message;

    /**
     * ValidatorAbstract constructor.
     *
     * @param array $options
     */
    public function __construct(array $options)
    {
        foreach ($options as $option => $val) {
            if (property_exists($this, $option)) {
                $this->$option = $val;
            }
        }
    }

    /**
     * 验证
     *
     * @param string $field
     * @param array $data
     * @return bool|string
     */
    abstract public function validator($field, array $data);

    /**
     * 获取字段名字
     *
     * @param $field
     * @return mixed
     */
    protected function getName($field)
    {
        if (is_array($this->name)) {
            return isset($this->name[$field]) ? $this->name[$field] : $field;
        }

        return $this->name ?: $field;
    }

    /**
     * 提示信息转换
     *
     * @param null $message
     * @param array|null $params
     * @param string $type
     * @return null
     */
    protected function message($message = null, array $params = null, $type = 'message')
    {
        return is_array($params) ? strtr(isset($this->$type) ? $this->$type : $message, $params) : $message;
    }
}