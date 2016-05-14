<?php

define('APP_ROOT', dirname(__DIR__));
define('APP_PATH', dirname(__DIR__) . '/application');

$application = new \Yaf\Application(APP_ROOT . "/conf/application.ini", 'develop');

$application->bootstrap()->run();

?>
