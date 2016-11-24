# yaf-skeleton

常用功能和一些的类库

## Required

- 安装YAF扩展 [yaf安装](http://php.net/manual/zh/yaf.installation.php)
- php.ini 配置: `yaf.use_namespace = 1`
- 使用命名空间引入需要的文件
 
## 说明

- 配置环境名 开发环境: `yaf.environ = develop` , 生产环境: `yaf.environ = production`
- 默认情况下`controllers`,`views`,`modules`,`library`,`models`,`plugins` 是在根 namespace

## 目录结构

```shelll
.
├── README.md
├── application
│   ├── Bootstrap.php
│   ├── controllers
│   │   ├── Error.php
│   │   └── Index.php
│   ├── defines
│   │   ├── Forum.php
│   │   └── User.php
│   ├── library
│   │   ├── Core
│   │   │   ├── Databases
│   │   │   └── Captcha
│   │   └── README.md
│   ├── models
│   │   ├── User.php
│   │   └── Forum.php
│   ├── modules
│   │   ├── Admin
│   │   │   ├── Bootstrap.php
│   │   │   ├── controllers
│   │   │   │   ├── Base.php
│   │   │   │   └── Index.php
│   │   │   └── views
│   │   │       ├── index
│   │   │       │   └── index.phtml
│   │   │       └── layout.phtml
│   │   └── Home
│   │       ├── controllers
│   │       │   ├── Forum.php
│   │       │   └── User.php
│   │       └── views
│   │           ├── user
│   │           │   ├── index.phtml
│   │           │   └── profile.phtml
│   │           └── forum
│   │               ├── list.phtml
│   │               └── detail.phtml
│   ├── plugins
│   │   └── Init.php
│   ├── services
│   │   ├── CommonService.php
│   │   ├── UserService.php
│   ├── tests
│   └── views
│       ├── error
│       │   └── error.phtml
│       └── index
│           ├── index.phtml
│           └── tpl.phtml
├── bin
│   └── run.php
├── composer.json
├── composer.lock
├── conf
│   ├── application.ini
│   ├── application.ini.example
│   └── routes.php
├── doc
│   └── project.sql
├── public
│   ├── favicon.ico
│   ├── index.php
│   └── assets
│       ├── attchments
│       ├── css
│       ├── img
│       └── js
└── storage
    ├── cache
    └── logs
```
 
## 功能介绍

### 路由

分三种路由,其中静态路由是默认路由,也是常用路由模式,如果需要其他特殊处理可以使用正则或Rewrite模式。

 - 静态路由
 - Rewrite路由
 - 正则路由
 
 Demo:
 
 ```php
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
     'news' => [
         'type' => 'rewrite',
         'match' => 'news/:id/',
         'route' => [
             'module' => 'Home',
             'controller' => 'News',
             'action' => 'detail',
         ],
     ],
 ];
 
 ```

### 视图view

### 校验

目前供支持10种格式校验
 
  - required
  - match
  - email
  - url
  - compare
  - length
  - in
  - number
  - mobile
  - date
        
```php

    $checkRules = [
        ['uid,group_id', 'required'],
        ['phone', 'match', 'pattern' => '/^1[34578]\d{9}[\d,]*$/', 'allowEmpty' => false],
        ['email', 'email', 'allowEmpty' => false],
        ['url', 'url', 'allowEmpty' => false],
        ['repassword', 'compare', 'target' => 'password', 'allowEmpty' => false],
        ['username', 'length', 'min' => 4, 'max' => 3000, 'allowEmpty' => false],
        ['status', 'in', 'range' => [0, 1], 'allowEmpty' => false],
        ['uid,group_id', 'number', 'min' => 1],
        ['phone', 'mobile', 'range' => [0, 1], 'allowEmpty' => false],
        ['birthday', 'date', 'format' => 'Y-m-d', 'allowEmpty' => false]
    ];
    
    $needCheckArr = [
        'email' => $email 
    ];
    if (Validator::validator($needCheckArr, $checkRules) !== true) {
        throw new \Exception('param error', Code::PARAMS_ERROR);
    }
```

## yaf 中文手册

 - http://php.net/manual/zh/book.yaf.php
 - http://www.laruence.com/manual/index.html
 
## 参考：
- http://www.laruence.com/manual/
- https://github.com/qieangel2013/yaf  db类
- https://github.com/melonwool/YafUse PDO不错，也支持读写分离
- https://github.com/mzsolti/yaf-phpport
- http://ju.outofmemory.cn/entry/191746
- https://fengqi.me/php/386.html
- http://www.open-open.com/lib/view/open1414396586434.html
- http://blog.csdn.net/treesky/article/details/50521437
- http://unixera.com/php/Yaf-yet-another-manual-for-human/
- https://github.com/xudianyang/yaf.app
- https://github.com/loncool/yaf-admin
- https://github.com/justmd5/yaf-twig-adapter 支持多模块配置
- https://github.com/mlboy/yaflib
- https://github.com/youbuwei/doeot   library 还算比较多
- https://github.com/chjphp/newtest   request分装
- https://github.com/sillydong/CZD_Yaf_Extension
- https://github.com/xudianyang/yaf.auto.complete Yaf在IDE下自动识别类、常量、自动补全方法名（Yaf IDE Auto Complete）
