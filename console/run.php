<?php

define('APP_ROOT', dirname(__DIR__));
define('APP_PATH', APP_ROOT . '/application');
define('APP_CONFIG_PATH', APP_ROOT . '/conf');
defined('CONFIG_PATH') or define('CONFIG_PATH', APP_CONFIG_PATH);

if ($argc < 2) {
    echo 'No command.';
    exit(1);
}

$request = new \Yaf\Request\Simple();

$commandFields = explode('/', trim($argv[1], '/'));
$fieldsCount = count($commandFields);
if ($fieldsCount == 2) {
    $request->setModuleName('Console');
    $request->setControllerName($commandFields[0]);
    $request->setActionName($commandFields[1]);
} elseif ($fieldsCount == 3) {
    $request->setModuleName($commandFields[0]);
    $request->setControllerName($commandFields[1]);
    $request->setActionName($commandFields[2]);
} else {
    echo 'Not found the command.';
    exit(1);
}

for ($i = 2; $i < $argc; $i++) {
    $optionOrigin = $argv[$i];
    if ($optionOrigin[0] !== '-') {
        echo 'Error option: ' . $optionOrigin;
        exit(1);
    }

    $option = explode('=', ltrim($optionOrigin, '-'), 2);
    $request->setParam($option[0], isset($option[1]) ? $option[1] : true);
}

$app = new \Yaf\Application(APP_ROOT . '/conf/application.ini', \Yaf\ENVIRON);
$app->bootstrap()->getDispatcher()->dispatch($request);
