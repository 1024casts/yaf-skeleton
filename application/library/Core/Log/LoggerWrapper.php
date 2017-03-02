<?php
namespace Core\Log;

// @todo should not wrapper
/**
 * Class LoggerWrapper
 *
 * @method error($message, array $context = array())
 * @method warning($message, array $context = array())
 * @method notice($message, array $context = array())
 * @method info($message, array $context = array())
 * @method debug($message, array $context = array())
 */
class LoggerWrapper
{
    public function __call($method, $params)
    {
        // @attention no implements in \Core\Log
        if (in_array($method, ['emergency', 'alert', 'critical'])) {
            return null;
        }

        return call_user_func_array("\\Core\\Log::{$method}", $params);
    }
}
