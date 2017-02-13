<?php
namespace Core\Rpc\Client\Json;

use Core\Support\Str;
use Core\Http\Client;
use Core\Support\Log;
use Core\Rpc\Client\Adapter;

/**
 * 内部JSON-RPC客户端实现
 *
 * usage sample:
 * // 定义
 * class Foo extends Inner
 * {
 *
 * }
 * // 使用
 * $rpcUrl = 'http://inner.example.com/private/rpc/call';
 * $module = 'srv.barService';
 * $foo = new Foo($rpcUrl, $module);
 * try {
 *     $rs = $foo->someRemoteMethod($arg1, $arg2, ...);
 *     // do something ...
 * } catch (\Exception $e) {
 *     // handling exception ...
 * }
 */
class Inner extends Adapter
{
    /**
     * 远程请求方法封装
     *
     * @param $method
     * @param $params
     *
     * @return bool|null
     */
    public function __call($method, $params) {
        $reqParams = [
            'id' => Str::random(16),
            'module' => $this->handler,
            'fun' => $method,
            'args' => $params
        ];

        $res = $this->doRequest($reqParams);

        return $this->parseRPCResult($res);
    }

    /**
     * 发起RPC请求
     *
     * @param $reqParams
     *
     * @return \Core\Http\Response|null
     * @throws \Exception
     */
    protected function doRequest($reqParams)
    {
        Log::debug('RPC json inner request params', $reqParams);

        if ($this->method == 'GET') {
            $res = (new Client())->get($this->url, ['params' => json_encode($reqParams)]);
        } else {
            $res = (new Client())->post($this->url, [], ['params' => json_encode($reqParams)]);
        }

        Log::debug('RPC json inner request result', ['result' => $res]);

        if ($res->code() != 200) {
            Log::warning('RPC error', ['url' => $this->url, 'params' => $reqParams, 'result' => $res]);

            throw new \Exception('RPC request failed');
        }

        return json_decode($res, true);
    }

    /**
     * 解析RPC请求结果
     *
     * @param $res
     *
     * @return bool|null
     * @throws \Exception
     */
    protected function parseRPCResult($res)
    {
        $rpcRes = $res['data'];

        if (!empty($rpcRes['output'])) {
            echo $rpcRes['output'];
        }

        if (!empty($rpcRes['exception'])) {
            $exception = new \Exception($rpcRes['exception']['msg'], $rpcRes['exception']['code']);

            $file = new \ReflectionProperty($exception, 'file');
            $file->setAccessible(true);
            $file->setValue($exception, $rpcRes['exception']['file']);

            $line = new \ReflectionProperty($exception, 'line');
            $line->setAccessible(true);
            $line->setValue($exception, $rpcRes['exception']['line']);

            throw $exception;
        }

        if (isset($rpcRes['status']) && $rpcRes['status'] != 200) {
            return false;
        }

        return isset($rpcRes['return']) ? $rpcRes['return'] : null;
    }

}
