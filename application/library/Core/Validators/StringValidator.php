<?php

namespace Core\Validators;

use Core\Exceptions\ParamsException;

/**
 * 字符串验证
 */
class StringValidator extends ValidatorAbstract
{
    /**
     * 最大长度
     *
     * @var int
     */
    public $max;

    /**
     * 最小长度
     *
     * @var int
     */
    public $min;

    /**
     * 指定相等长度
     * @var int
     */
    public $equal;

    /**
     * 太短提示信息
     *
     * @var string
     */
    public $tooShort;

    /**
     * 太长提示信息
     *
     * @var string
     */
    public $tooLong;

    /**
     * 长度不相等提示信息
     *
     * @var string
     */
    public $notEqual;

    /**
     * 是否允许为空
     * meaning that if the attribute is empty, it is considered valid.
     *
     * @var bool
     */
    public $allowEmpty = true;

    /**
     * 用什么函数检查
     *
     * @var string
     */
    public $checkBy = 'mb_strlen';

    /**
     * 验证
     *
     * @param string $field
     * @param array $data
     *
     * @return bool|string
     * @throws ParamsException
     */
    public function validator($field, array $data)
    {
        $value = isset($data[$field]) ? $data[$field] : null;
        if ($value === '' || $value === null) {
            return $this->allowEmpty ? true : $this->message('{fieldName}不能为空！', [
                '{fieldName}' => $this->getName($field),
            ], 'emptyMsg');
        }

        if (!is_callable($this->checkBy)) {
            throw new ParamsException('checkBy', 'not callable');
        }

        $len = call_user_func($this->checkBy, $value);
        if ($this->min !== null && $this->min > $len) {
            return $this->message('{fieldName}长度不能小于{min}！', [
                '{fieldName}' => $this->getName($field),
                '{min}' => $this->min,
            ], 'tooSmall');
        }

        if ($this->max !== null && $this->max < $len) {
            return $this->message('{fieldName}长度不能大于{max}！', [
                '{fieldName}' => $this->getName($field),
                '{max}' => $this->max,
            ], 'tooBig');
        }

        if ($this->equal !== null && $this->equal != $len) {
            return $this->message('{fieldName}长度不等于{equal}！', [
                '{fieldName}' => $this->getName($field),
                '{equal}' => $this->equal,
            ], 'notEqual');
        }

        return true;
    }

    /**
     * 验证值
     *
     * @param $value
     * @return bool|string
     */
    protected function validatorValue($value)
    {
        if ($value === null) {
            return $this->allowEmpty;
        }

        if (preg_match($this->pattern, $value)) {
            return true;
        }

        return false;
    }
}