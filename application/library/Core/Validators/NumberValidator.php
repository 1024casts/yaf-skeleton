<?php

namespace Core\Validators;

/**
 * 数字验证
 */
class NumberValidator extends ValidatorAbstract
{
    /**
     * 只允许整形
     *
     * @var bool
     */
    public $intOnly = false;

    /**
     * 是否允许为空
     * meaning that if the attribute is empty, it is considered valid.
     *
     * @var bool
     */
    public $allowEmpty = true;

    /**
     * 最大值多少,留空则不判断
     *
     * @var integer|float
     */
    public $max;

    /**
     * 最小值多少,留空则不判断
     *
     * @var integer|float
     */
    public $min;

    /**
     * 太大的提示消息
     *
     * @var string
     */
    public $tooBig;

    /**
     * 太小的提示消息
     *
     * @var string
     */
    public $tooSmall;

    /**
     * 为空提示语
     *
     * @var string
     */
    public $emptyMsg;

    /**
     * 整形的正则表达式
     *
     * @var string
     */
    public $intPattern = '/^[+-]?\d+$/';

    /**
     * 数字的正则表达式
     *
     * @var string
     */
    public $numberPattern = '/^[-+]?(0|[1-9]\d*)(\.\d+)?$/';

    /**
     * 验证
     *
     * @param string $field
     * @param array $data
     * @return bool|string
     */
    public function validator($field, array $data)
    {
        $value = isset($data[$field]) ? $data[$field] : null;
        if ($value === '' || $value === null) {
            return $this->allowEmpty ? true : $this->message('{fieldName}不是整数！', [
                '{fieldName}' => $this->getName($field),
            ], 'emptyMsg');
        }

        if ($this->min !== null && $this->min > $value) {
            return $this->message('{fieldName}不能小于{min}！', [
                '{fieldName}' => $this->getName($field),
                '{min}' => $this->min,
            ], 'tooSmall');
        }

        if ($this->max !== null && $this->max < $value) {
            return $this->message('{fieldName}不能大于{max}！', [
                '{fieldName}' => $this->getName($field),
                '{max}' => $this->max,
            ], 'tooBig');
        }

        if (!preg_match($this->intOnly ? $this->intPattern : $this->numberPattern, $value)) {
            return $this->message('{fieldName}不是' . ($this->intOnly ? '整数' : '数字') . '！', [
                '{fieldName}' => $this->getName($field),
            ]);
        }

        return true;
    }
}