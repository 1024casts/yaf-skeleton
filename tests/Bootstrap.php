<?php

define('APP_ROOT', dirname(__DIR__));
define('APP_PATH', APP_ROOT . '/application');
define('CONFIG_PATH', APP_ROOT . '/conf');

// @attention load yaf global library, load config, then bootstrap
$app = new \Yaf\Application(CONFIG_PATH . '/application.ini');
$app->bootstrap();

$loader = require dirname(__DIR__) . '/vendor/autoload.php';
$loader->addPsr4("Tests\\", __DIR__);
