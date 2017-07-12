# yaf-skeleton

yaf 基本功能介绍（含常用类库）

## 安装

`composer create-project phpcasts/yaf-skeleton yaf-demo dev-master -vvv`

加速版：

`composer create-project phpcasts/yaf-skeleton yaf-demo dev-master --repository-url https://packagist.phpcomposer.com -vvv`

## Required

- 安装YAF扩展 [yaf安装](http://php.net/manual/zh/yaf.installation.php)
- php.ini 配置: 开启命名空间 `yaf.use_namespace = true`
- 使用命名空间引入需要的文件
- composer

## 运行

```shell
cd yaf-demo
php -S localhost:8080 public/index.php
open localhost:8080
```

应该就可以看到 `Hello World!` 了，有兴趣的同学可以继续看后面的大概介绍。
 
## 说明

- 配置环境 开发环境: `yaf.environ = develop` , 生产环境: `yaf.environ = production`
- 默认情况下 `controllers`,`views`,`modules`,`library`,`models`,`plugins` 是在根 namespace
- service目录的namespace 是配置在 `App\Services` 下,需在 `composer.json`的`autoload`下的`psr-4`配置
- 第三方类库使用Composer安装
- 开发规范遵循 `PSR2`, `PSR4`规范

## 目录结构

```shell
.
├── README.md
├── application
│   ├── Bootstrap.php                       // app启动文件
│   ├── controllers                         // 默认controller
│   │   ├── Error.php                       // 错误controller, 出错时会调用该文件
│   │   └── Index.php
│   ├── constants                           // 常量定义目录,按模块划分文件
│   │   ├── Forum.php
│   │   └── User.php
│   ├── library                             // 框架lib库, 所有自定的都可以写到这里来
│   │   ├── Core
│   │   │   ├── Caches
│   │   │   ├── Captcha
│   │   │   ├── Controllers
│   │   │   ├── Databases
│   │   │   ├── Http
│   │   │   ├── Support
│   │   │   ├── Upload
│   │   │   ├── Validators
│   │   │   └── Views
│   │   └── README.md
│   ├── models                              // model目录
│   │   ├── User.php
│   │   └── Forum.php
│   ├── modules                             // 模块目录,里面可以有多个模块
│   │   ├── Api                             // APP接口位置
│   │   ├── Console                         // 脚本目录
│   │   ├── Admin                           // 后台目录
│   │   │   ├── Bootstrap.php               // Admin的Bootstrap文件,只对Admin生效
│   │   │   ├── controllers                 // controller 目录
│   │   │   │   ├── Base.php
│   │   │   │   └── Index.php
│   │   │   └── views                       // 模板目录
│   │   │       ├── index                   // 业务目录
│   │   │       │   └── index.phtml
│   │   │       └── layout.phtml            // 布局文件
│   │   └── Home                            // 前端目录
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
│   ├── plugins                             // 插件目录
│   │   └── Init.php
│   ├── services                            // 业务逻辑封装
│   │   ├── CommonService.php
│   │   ├── UserService.php
│   ├── tests                               // 单元测试相关
│   └── views                               // 单模块的试图目录
│       ├── error
│       │   └── error.phtml
│       └── index
│           ├── index.phtml
│           └── tpl.phtml
├── bin
│   ├── console
│   └── run
├── composer.json
├── composer.lock
├── conf                                    // 配置目录
│   ├── application.ini
│   ├── application.ini.example
│   └── routes.php                          // 通过bootstrap加载
├── public
│   ├── favicon.ico
│   ├── index.php                           // 单入口文件
│   └── assets
│       ├── css
│       ├── img
│       └── js
└── storage
    ├── cache                               // 缓存目录
    └── logs                                // 日志目录
```
 
## 功能介绍

### 配置文件

`conf/application.ini` 是整个框架的配置文件,默认系统的命名是使用驼峰式的。
用户自定义的配置一般也写到这里即可,使用下划线分割。

### Bootstrap

 - 1、`application`目录下有一个总的`Bootstrap.php`文件,这里可以加载全局需要用到的`ORM`,`Plugins`,`Composer`,`Route`,`Config`等等, 此文件是默认存在的。
 - 2、`application/modules`目录下各个module里也可以定义各自的`Bootstrap.php`, 在这里可以做一些当前模块的全局处理,比如检查用户是否登录。

### 路由

分三种路由,其中静态路由是默认路由,也是常用路由模式,如果需要其他特殊处理可以使用正则或Rewrite模式。

 - 静态路由(默认)
 - 简单(Simple)路由
 - Supervar路由
 - Map路由
 - Rewrite路由
 - 正则(Regex)路由
 
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
     // 或
     'news' => [
          'type' => 'rewrite',
          'match' => 'resource/:c/:a/:id/',
          'route' => [
              'module' => 'Home',
              'controller' => 'news',
              'action' => 'detail',
          ],
      ],
 ];
 
 ```

### 控制器 Controller

命名规则: 第一个字母大写,紧跟后面的必须小写。class名称同文件名。
```
错误: LiveStream.php
正确: Livestream.php
```
同时支持JSONP返回,只需要在get url时传入 _callback即可。

### ORM

集成 `Laravel`的`Eloquent`的ORM, `Eloquent` 比较强大也比较好用,玩够一个足矣。 
[详细介绍](https://laravel.com/docs/5.3/eloquent)

### 视图

默认在`application/views`下, 如果是多模块则放到对应的modules下的views里。
也可以通过`Composer`加载 `Laravel`的`Blade`或者 `Symfony`的`Twig`模板引擎,
当然需要在Bootstrap里初始化加载一下

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

### 自动加载

可以加载本地类库,默认在`application/library`里, 也可以在`conf/application.ini`或php.ini的yaf配置里指定。

### 插件

可以自己写一些插件来满足业务需要, 最后通过`application`下的`Bootstrap.php`里的`_initPlugin`来调用。

### 脚本

脚本一般放在 `application/modules/Console/controllers` 目录下, 写法和其他模块里`controller`的写法一致。

使用:
```php
php bin/run Test/test   // Test 控制器下的test action
```

### 日志

日志使用比较简单,在需要记录日志的电饭锅加入以下代码:
```php
use Core\Log;

Log::info('日志标识', ['param1' => $param1, 'param2' => $param2,...]);
```
看后在命令行下可以查看:
```shell
tail -f storage/logs/2016-11-29.log
```

PS: 依赖`monolog` package

### 异常和错误

如果配置文件中`appliation.dispatcher.throwException` 设为1或true,Yaf会抛异常, 否则则会触发错误。
当Yaf遇到未捕获异常的时候, 就会把运行权限, 交给当前模块的Error Controller的Error Action动作, 而异常或作为请求的一个参数, 传递给Error Action.
错误页默认在`application/views/error/error.phtml`, 可以进行自定义处理。

>  [我们什么时候应该使用异常?](http://www.laruence.com/2012/02/02/2515.html)

### 依赖注入(Dependency injection)

DI 一般通过contructor来注入（依赖注入的一种方式), 补充注入过程

@todo 增加demo

### 事件管理(EventManager)

@todo 增加demo

### 单元测试

PHPUnit

## 命令行工具

```shell
php bin/console  // 查看可用命令
php bin/console make:controller Articles	// 创建控制器
php bin/console make:model Articles // 创建模型
php bin/console make:plugin Test	// 创建插件
```

## yaf 中文手册

 - http://php.net/manual/zh/book.yaf.php **推荐** 
 - http://www.laruence.com/manual/index.html
 
## 参考

- http://www.laruence.com/manual/
- https://github.com/qieangel2013/yaf  db类
- https://github.com/melonwool/YafUse PDO不错，也支持读写分离
- https://github.com/mzsolti/yaf-phpport
- http://ju.outofmemory.cn/entry/191746
- https://fengqi.me/php/386.html
- http://www.open-open.com/lib/view/open1414396586434.html
- http://blog.csdn.net/treesky/article/details/50521437
- http://unixera.com/php/Yaf-yet-another-manual-for-human/
- https://github.com/xudianyang/yaf.app 推荐阅读（含XHProf, 队列:php-resque）
- https://github.com/loncool/yaf-admin
- https://github.com/justmd5/yaf-twig-adapter 支持多模块配置
- https://github.com/mlboy/yaflib
- https://github.com/youbuwei/doeot   library 还算比较多
- https://github.com/chjphp/newtest   request分装
- https://github.com/sillydong/CZD_Yaf_Extension
- https://github.com/xudianyang/yaf.auto.complete Yaf在IDE下自动识别类、常量、自动补全方法名（Yaf IDE Auto Complete）
