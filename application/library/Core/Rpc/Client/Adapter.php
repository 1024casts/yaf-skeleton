<?php
namespace Core\Rpc\Client;

abstract class Adapter
{
    /**
     * RPC-HTTP请求方式, 默认为GET
     * @var string
     */
    protected $method = 'GET';

    /**
     * 远程服务URL地址, slug url
     * @var string
     */
    protected $url;

    /**
     * 远程被调用服务, 可通过DI获取
     * @var string
     */
    protected $handler;

    /**
     * 构造函数
     *
     * @param string $url 远程服务URL地址, slug url
     * @param string $handler 远程被调用服务, 可通过DI获取
     */
    public function __construct($url, $handler) {
        $this->url = $url;
        $this->handler = $handler;
    }

}
