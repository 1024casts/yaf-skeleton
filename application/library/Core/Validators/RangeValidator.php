<?php

namespace Core\Validators;

/**
 * 比较是否在范围内
 */
class RangeValidator extends ValidatorAbstract
{
    /**
     * 枚举值
     *
     * @var array
     */
    public $range;

    /**
     * 是否允许为空
     * meaning that if the attribute is empty, it is considered valid.
     *
     * @var bool
     */
    public $allowEmpty = true;

    /**
     * 是否严格比较
     *
     * @var bool
     */
    public $isStrict = false;

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
            return $this->allowEmpty ? true : $this->message('{fieldName}不在允许的范围内！', [
                '{fieldName}' => $this->getName($field),
            ], 'emptyMsg');
        }

        if (in_array($value, $this->range, $this->isStrict)) {
            return true;
        }

        return $this->message('{fieldName}不在允许的范围内！', [
            '{fieldName}' => $this->getName($field),
        ]);
    }
}