<?php

namespace Core\Validators;

/**
 * 网址验证
 */
class UrlValidator extends ValidatorAbstract
{
    /**
     * the regular expression used to validate the attribute value.
     *
     * @var string
     */
    public $pattern = '/^{schemes}:\/\/(([A-Z0-9][A-Z0-9_-]*)(\.[A-Z0-9][A-Z0-9_-]*)+)/i';

    /**
     * 允许的Schemes
     *
     * @var array
     */
    public $validSchemes = ['http', 'https'];

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

        return $this->message('{fieldName}不是链接！', [
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

        $pattern = str_replace('{schemes}', '(' . implode('|', $this->validSchemes) . ')', $this->pattern);
        if (preg_match($pattern, $value)) {
            return true;
        }

        return false;
    }
}