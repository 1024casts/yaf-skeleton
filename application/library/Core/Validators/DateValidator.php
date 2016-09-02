<?php

namespace Core\Validators;

/**
 * 日期验证
 */
class DateValidator extends ValidatorAbstract
{
    /**
     * 日期格式,参考函数date
     *
     * @var string
     */
    public $format = 'Y-m-d H:i:s';

    /**
     * 是否允许为空
     * meaning that if the attribute is empty, it is considered valid.
     *
     * @var bool
     */
    public $allowEmpty = true;

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
            return $this->allowEmpty ? true : $this->message('{fieldName}不能为空！', [
                '{fieldName}' => $this->getName($field),
            ], 'emptyMsg');
        }

        if (date($this->format, strtotime($value)) == $value) {
            return true;
        }

        return $this->message('{fieldName}不是合法的日期！', [
            '{fieldName}' => $this->getName($field),
        ]);
    }
}