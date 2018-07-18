<?php

/**
 * ServiceProvider 可以在 app/Providers中新增，也可以通过在此配置文件中添加
 */
return [
    /** ====================must setting, being used everywhere==================== */

    'sessionBag' => function () {
        return new PHPCasts\Yaf\Caches\Memory();
    },
];
