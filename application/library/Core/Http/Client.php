<?php

namespace Core\Http;

use Core\Log;
use CURLFile;
use Yaf\Registry;

/**
 * Http 客户端
 *
 * 示例:
 * # GET 请求
 * $response = (new Client())->get('http://www.baidu.com');
 *
 * # GET 传参
 * $response = (new Client())->get('http://www.baidu.com', ['kw' => '这里的数据拼在URL后']);
 *
 * # POST 请求
 * $response = (new Client())->post('http://www.baidu.com', ['kw' => '这里的数据拼在URL后'], ['content' => '这里是POST的数据']);
 *
 * # POST 请求, 上传文件
 * $response = (new Client())->post('http://www.baidu.com', [], ['content' => '这里是POST的数据'], ['upload' => '这里是上传的文件']);
 *
 * # 判断请求是否成功
 * if ($response === null) {
 *     die('请求失败了!');
 * }
 */
class Client
{
    /**
     * Curl 选项
     *
     * @var array
     */
    protected $options = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 1,
        CURLOPT_TIMEOUT => 3,
        CURLOPT_AUTOREFERER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS => 3, // 最多的 HTTP 重定向次数
    ];

    /**
     * 请求头
     *
     * @var array
     */
    protected $headers = [
        'User-Agent' => 'CoreHttp/1.0.0',
    ];

    /**
     * 是否启用并发请求
     *
     * @var bool
     */
    protected $isMulti = false;

    /**
     * Client constructor.
     */
    public function __construct($isMulti = false)
    {
        $this->isMulti = $isMulti;

        $config = Registry::get('config');
        if (isset($config['http']['timeout'])) {
            $this->setOption(CURLOPT_TIMEOUT, $config['http']['timeout']);
        }
        if (isset($config['http']['userAgent'])) {
            $this->setHeader('User-Agent', $config['http']['userAgent']);
        }
    }

    /**
     * 设置选项
     *
     * @param int $time 超时时间,单位秒
     * @return $this
     */
    public function timeout($time)
    {
        return $this->setOption(CURLOPT_TIMEOUT, $time);
    }

    /**
     * 设置选项
     *
     * @param int $key CURL选项
     * @param mixed $value 选项值
     * @return $this
     */
    public function setOption($key, $value)
    {
        $this->options[$key] = $value;
        return $this;
    }

    /**
     * 添加一个请求头
     *
     * @param string $name 头名称
     * @param string $value 头内容
     * @return $this
     */
    public function addHeader($name, $value)
    {
        if (isset($this->headers[$name])) {
            if (is_array($this->headers[$name])) {
                $this->headers[$name][] = $value;
            } else {
                $this->headers[$name] = $value;
            }
        } else {
            $this->headers[$name] = [$value];
        }

        return $this;
    }

    /**
     * 批量添加Header
     *
     * @param array $headers 要添加的Header头
     * @return $this
     */
    public function addHeaders(array $headers)
    {
        foreach ($headers as $name => $value) {
            $this->addHeader($name, $value);
        }

        return $this;
    }

    /**
     * 设置一个请求头
     *
     * @param string $name 头名称
     * @param string $value 头内容
     * @return $this
     */
    public function setHeader($name, $value)
    {
        if (isset($this->headers[$name])) {
            if (is_array($this->headers[$name])) {
                $this->headers[$name] = (array)$value;
            } else {
                $this->headers[$name] = $value;
            }
        } else {
            $this->headers[$name] = (array)$value;
        }

        return $this;
    }

    /**
     * 批量设置Header
     *
     * @param array $headers 要设置的请求头
     * @return $this
     */
    public function setHeaders(array $headers)
    {
        foreach ($headers as $name => $value) {
            $this->setHeader($name, $value);
        }

        return $this;
    }

    /**
     * HEAD Request
     *
     * @param string $url 链接地址
     * @param array $query 发送的数据
     * @return Response|null 请求失败返回null,成功返回Response对象
     */
    public function head($url, array $query = [])
    {
        $url = $this->buildUrl($url, $query);
        $handle = $this->getHandler();

        curl_setopt_array($handle, [
            CURLOPT_CUSTOMREQUEST => 'HEAD',
            CURLOPT_NOBODY => true,
        ]);

        return $this->run($handle, $url);
    }

    /**
     * GET Request
     *
     * @param string $url 链接地址
     * @param array $query 发送的数据
     * @return Response|null 请求失败返回null,成功返回Response对象
     */
    public function get($url, array $query = [])
    {
        $url = $this->buildUrl($url, $query);
        $handle = $this->getHandler();

        return $this->run($handle, $url);
    }

    /**
     * POST Request
     *
     * @param string $url 链接地址
     * @param array $query 拼接在URL后的数据
     * @param array $data 发送的数据
     * @param array $files 文件列表
     * @return Response|null 请求失败返回null,成功返回Response对象
     */
    public function post($url, array $query = [], array $data = [], array $files = [])
    {
        $url = $this->buildUrl($url, $query);

        if ($files) {
            foreach ($files as $key => $file) {
                $data[$key] = new CURLFile($file['tmp_name']);
            }
        } else {
            $data = http_build_query($data);
        }

        $handle = $this->getHandler();

        curl_setopt_array($handle, [
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $data,
        ]);

        return $this->run($handle, $url);
    }

    /**
     * GET Request
     *
     * @param string $url 链接地址
     * @param array $query 拼接在URL后的数据
     * @param array $data 发送的数据
     * @return Response|null 请求失败返回null,成功返回Response对象
     */
    public function delete($url, array $query = [], array $data = [])
    {
        $url = $this->buildUrl($url, $query);
        $handle = $this->getHandler();

        curl_setopt_array($handle, [
            CURLOPT_CUSTOMREQUEST => 'DELETE',
            CURLOPT_POSTFIELDS => http_build_query($data),
        ]);

        return $this->run($handle, $url);
    }

    /**
     * 获取CURL Handler
     *
     * @return mixed
     */
    protected function getHandler()
    {
        $handler = curl_init();

        return $handler;
    }

    /**
     * 执行
     *
     * @param resource $handler
     * @param string $url
     * @return Response|null
     */
    protected function run($handler, $url)
    {
        $headers = [
            'X-REQUEST-ID: ' . $this->requestId(),
        ];
        foreach ($this->headers as $name => $values) {
            if (is_array($values)) {
                foreach ($values as $value) {
                    $headers[] = $name . ': ' . $value;
                }
            } else {
                $headers[] = $name . ': ' . $values;
            }
        }

        $options = $this->options;
        $options[CURLINFO_HEADER_OUT] = true;
        $options[CURLOPT_HEADER] = true;
        $options[CURLOPT_URL] = $url;
        $options[CURLOPT_HTTPHEADER] = $headers;

        curl_setopt_array($handler, $options);

        // 如果是并发请求则停止向下执行
        if ($this->isMulti) {
            return $handler;
        }

        $result = curl_exec($handler);
        if ($result === false) {
            Log::warning('Curl error: ' . curl_error($handler), [$url, 'trace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS & DEBUG_BACKTRACE_PROVIDE_OBJECT)]);

            return null;
        }

        return new Response($result, $handler);
    }

    /**
     * URL Build
     *
     * @param string $url
     * @param array $data
     * @return string
     */
    protected function buildUrl($url, array $data)
    {
        if (!$data) {
            return $url;
        }

        if (strpos($url, '?') === false) {
            $url .= '?';
        } elseif (substr($url, 0, -1) != '?') {
            $url .= '&';
        }

        return $url . http_build_query($data);
    }

    /**
     * 获取请求ID
     *
     * @return string
     */
    protected function requestId()
    {
        return isset($_SERVER['HTTP_X_REQUEST_ID']) ? $_SERVER['HTTP_X_REQUEST_ID'] : '-';
    }
}
