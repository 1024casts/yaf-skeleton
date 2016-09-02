<?php

namespace Core\Validators;

/**
 * 邮箱验证
 */
class MobileValidator extends ValidatorAbstract
{
    /**
     * 手机号码正则
     *
     * @var string
     */
    public $pattern = '/^1[34578]\d{9}$/';

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
        if ($this->validatorValue($value)) {
            return true;
        }

        return $this->message('{fieldName}不是手机号！', [
            '{fieldName}' => $this->getName($field),
        ]);
    }

    /**
     * 验证值
     *
     * @param $value
     * @return bool|string
     */
    protected function validatorValue($value)
    {
        if (!$value) {
            return $this->allowEmpty;
        }

        if (preg_match($this->pattern, $value)) {
            return true;
        }

        return false;
    }
}