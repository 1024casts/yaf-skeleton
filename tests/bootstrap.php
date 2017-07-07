<?php

define('APP_ROOT', dirname(__DIR__));
define('APP_PATH', APP_ROOT . '/application');
define('APP_CONFIG_PATH', APP_ROOT . '/conf');

$application = new \Yaf\Application(APP_ROOT . "/conf/application.ini", \Yaf\ENVIRON);
$application->bootstrap()->run();