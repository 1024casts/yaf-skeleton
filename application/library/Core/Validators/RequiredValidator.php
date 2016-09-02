<?php

namespace Core\Validators;

/**
 * 验证不能为空
 */
class RequiredValidator extends ValidatorAbstract
{
    /**
     * 检查是否为空
     *
     * @param string $field
     * @param array $data
     * @return bool|string
     */
    public function validator($field, array $data)
    {
        if (!isset($data[$field]) || $data[$field] === '') {
            return $this->message('{fieldName}不能为空！', [
                '{fieldName}' => $this->getName($field),
            ]);
        }

        return true;
    }
}