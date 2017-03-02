<?php

// 全局事件($di['eventsManager'])监听者配置
return [
    // 配置方式:
    // event => handler or event => [handler1, handler2, ...]
    // 处理handler:
    // handler => closure function like function (Event $event, mixed $source, mixed $eventData) {...}

    'payment' => [
        // \App\Services\Listener\Payment::class,
    ],
    'flow:delay' => [
        // [\App\Services\Listener\Flow::class, 'handleEvent']
    ],
];
