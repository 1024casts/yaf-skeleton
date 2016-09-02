<?php

namespace Core\Validators;

/**
 * 字段对比验证
 */
class CompareValidator extends ValidatorAbstract
{
    /**
     * 和谁做比较
     *
     * @var string
     */
    public $target;

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
        $targetValue = isset($data[$this->target]) ? $data[$this->target] : null;
        if ($value === $targetValue) {
            return true;
        }

        return $this->message('{fieldName}与{targetName}不相等！', [
            '{fieldName}' => $this->getName($field),
            '{targetName}' => $this->getName($this->target),
        ]);
    }
}