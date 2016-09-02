<?php

namespace Core\Http;

use Core\Exceptions\RuntimeException;
use Core\Log;

/**
 * Http 并发客户端
 *
 * 示例:
 *   $mc = new MultiClient();
 *   $mc->timeout(3);
 *   $mc->add((new Client(true))->post('https://www.baidu.com/'), function($res, $err) {
 *       echo $
 *   });
 *   $mc->add((new Client(true))->get('https://www.google.com/'), function($res, $err) {
 *       echo print_r(func_get_args(), true);
 *   });
 *   $mc->get('http://www.zhihu.com/', function($res, $err) {
 *       echo print_r(func_get_args(), true);
 *   });
 *   $mc->post('http://www.douban.com/', function($res, $err) {
 *       echo print_r(func_get_args(), true);
 *   });
 *   $mc->exec();
 */
class MultiClient
{
    /**
     * 所有的Handlers
     *
     * @var array
     */
    protected $handlers = [];

    /**
     * 默认的Client
     *
     * @var Client
     */
    protected $defaultClient;

    /**
     * 获取默认客户端
     *
     * @return Client
     */
    protected function getClient()
    {
        if ($this->defaultClient) {
            return $this->defaultClient;
        }

        return $this->defaultClient = new Client(true);
    }

    /**
     * 方法映射等
     *
     * @param $name
     * @param $args
     * @return $this|MultiClient
     * @throws RuntimeException
     */
    public function __call($name, $args)
    {
        $client = $this->getClient();
        if (!method_exists($client, $name)) {
            throw new RuntimeException('Call undefined method: ' . __CLASS__ . ':' . $name);
        }

        if (in_array($name, ['get', 'post', 'head', 'delete'])) {
            $lastIndex = count($args) - 1;
            if ($args && is_callable($args[$lastIndex])) {
                return $this->add(call_user_func_array([$client, $name], array_slice($args, 0, -1)), $args[$lastIndex]);
            }

            return $this->add(call_user_func_array([$client, $name], $args));
        }

        call_user_func_array([$client, $name], $args);
        return $this;
    }

    /**
     * 添加一个的Handler
     *
     * @param $handler
     * @param callable|null $callback
     * @return $this
     */
    public function add($handler, callable $callback = null)
    {
        $this->handlers[] = [$handler, $callback];

        return $this;
    }

    /**
     * 执行并发
     */
    public function exec()
    {
        $mh = curl_multi_init();
        foreach ($this->handlers as $handler) {
            curl_multi_add_handle($mh, $handler[0]);
        }

        do {
            curl_multi_exec($mh, $active);
            if (!$active) {
                break;
            }

            usleep(1000);
        } while (true);

        foreach ($this->handlers as $handler) {
            $response = curl_multi_getcontent($handler[0]);
            curl_multi_remove_handle($mh, $handler[0]);

            $error = null;
            if (!$response) {
                $info = curl_getinfo($handler[0]);
                if ($info['http_code'] != 200) {
                    $error = 'Curl failed!';
                    Log::warning($error, [
                        'info' => $info,
                        'trace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS & DEBUG_BACKTRACE_PROVIDE_OBJECT, 3)
                    ]);
                }
            }

            if (!$handler[1]) {
                continue;
            }

            $tmp = null;
            if (!$error) {
                $tmp = new Response($response, $handler[0]);
            }

            call_user_func($handler[1], $tmp, $error);
        }

        curl_multi_close($mh);
    }
}
