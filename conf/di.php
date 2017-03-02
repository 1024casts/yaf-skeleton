<?php

return [
    /** ====================must setting, being used everywhere==================== */
    'assert' => function () {
        $assert = new \Core\Assert();
        $assert->setCode(\App\Defines\Code::class);

        return $assert;
    },
    'crypt' => function ($c) {
        $key = $c['config']['crypt']['key'];

        class AES extends phpseclib\Crypt\AES
        {
            public function encrypt($plaintext)
            {
                return base64_encode(parent::encrypt($plaintext));
            }

            public function decrypt($ciphertext)
            {
                return parent::decrypt(base64_decode($ciphertext));
            }
        }

        $aes = new AES();
        $aes->setKey($key);

        return $aes;
    },

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
            return new Core\LoggerWrapper();
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
