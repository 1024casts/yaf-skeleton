<?php
/**
 * 路由
 *
 * File: routes.php
 */
return [
    // 正则路由
    'news' => [
        'type' => 'regex',
        'match' => '/news\/([\d]+)/',
        'route' => [
            'module' => 'Home',
            'controller' => 'News',
            'action' => 'detail',
        ],
        'map' => [ //参数
            '1' => 'id',
        ],
    ],

    // rewrite路由
    'user' => [
        'type' => 'rewrite',
        'match' => 'user/:id/',
        'route' => [
            'module' => 'Home',
            'controller' => 'user',
            'action' => 'profile',
        ],
    ],
];