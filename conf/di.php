<?php

return [
    /** ====================must setting, being used everywhere==================== */

    // global events manager for whole system
    'eventsManager' => \Core\Events\Manager::class,

    'config' => function () {
        return \Yaf\Registry::get('config');
    },

    'logger' => function () {
        // @todo more compatible
        if (strtolower(Yaf\Dispatcher::getInstance()->getRequest()->getModuleName()) == 'console'
            && getenv('LOG_TO_CONSOLE')
        ) {
            return new Monolog\Logger('console-name', [new Monolog\Handler\StreamHandler('php://output')]);
        } else {
            return new Core\Log\LoggerWrapper();
        }
    },

    'sessionBag' => function () {
        return new \Core\Caches\Memory();
    },
    'session' => function () {
        return \Yaf\Session::getInstance();
    },

    /** ==============================custom setting============================== */

    'cache' => function ($c) {
        return new \Core\Caches\Redis($c['redis']);
    },
    'redis' => function ($c) {
        $config = $c['config']['redis']['default'];

        $redis = new \Redis();
        $redis->connect($config['host'], $config['port'], $config['timeout']);

        return $redis;
    },
];
