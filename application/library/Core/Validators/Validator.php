<?php

namespace Core\Validators;

use Core\Exceptions\ParamsException;

/**
 * 验证器
 *
 * 为避免多次IO,暂时在一个里面,以后可以考虑拆开
 */
class Validator
{
    /**
     * 内建验证器
     *
     * @var array
     */
    public static $builtInValidators = [
        'required' => 'RequiredValidator',
        'match' => 'RegularExpressionValidator',
        'email' => 'EmailValidator',
        'url' => 'UrlValidator',
        'compare' => 'CompareValidator',
        'length' => 'StringValidator',
        'in' => 'RangeValidator',
        'number' => 'NumberValidator',
        'mobile' => 'MobileValidator',
        'date' => 'DateValidator',
    ];

    /**
     * 验证
     *
     * @param array $data 待验证的数据
     * @param array $rules 验证规则
     * @param bool $isReturnAll 是否一次返回所有错误
     * @return bool|string|array
     * @throws ParamsException
     */
    public static function validator(array $data, array $rules, $isReturnAll = false)
    {
        $errors = [];

        foreach ($rules as $rule) {
            if (count($rule) < 2) {
                throw new ParamsException('rules', '规则错误');
            }

            $validator = $rule[1];
            if (!isset(static::$builtInValidators[$validator])) {
                throw new ParamsException('rules', "[{$validator}]规则不存在");
            }

            $validator = __NAMESPACE__ . '\\' . static::$builtInValidators[$validator];

            $fields = explode(',', $rule[0]);
            unset($rule[0], $rule[1]);

            $validatorObj = new $validator($rule);
            foreach ($fields as $field) {
                if (isset($errors[$field])) {
                    continue;
                }

                $result = $validatorObj->validator($field, $data);
                if ($result === true) {
                    continue;
                }

                if (!$isReturnAll) {
                    return $result;
                }

                $errors[$field] = $result;
            }
        }

        return $errors ?: true;
    }
}