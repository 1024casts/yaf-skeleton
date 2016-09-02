<?php

namespace Core\Http;

/**
 * 响应结果
 */
class Response
{
    /**
     * 响应结果
     *
     * @var string
     */
    protected $body = '';

    /**
     * 响应头
     *
     * @var array
     */
    protected $headers;

    /**
     * 原始响应头
     *
     * @var string
     */
    protected $originHeaders;

    /**
     * curl handle
     *
     * @var resource
     */
    protected $handle;

    /**
     * Response constructor.
     * @param $result
     * @param $handle
     */
    public function __construct($result, $handle)
    {
        $headerSize = curl_getinfo($handle, CURLINFO_HEADER_SIZE);
        $this->originHeaders = substr($result, 0, $headerSize - 4); // 结尾的连续两个CRLF
        $this->body = substr($result, $headerSize) ?: '';
        $this->handle = $handle;
    }

    /**
     * 获取响应状态码
     *
     * @return mixed
     */
    public function code()
    {
        return curl_getinfo($this->handle, CURLINFO_HTTP_CODE);
    }

    /**
     * 获取内容
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * 获取响应头
     *
     * @param $key
     * @return string|array|null
     */
    public function getHeader($key)
    {
        $headers = $this->getHeaders();
        if (isset($headers[$key])) {
            return $headers[$key];
        }

        return null;
    }

    /**
     * 获取所有响应头
     *
     * @return array
     */
    public function getHeaders()
    {
        if ($this->headers) {
            return $this->headers;
        }

        $this->headers = [
            'STATUS' => $this->code(),
        ];

        $originHeaders = explode("\r\n\r\n", $this->originHeaders);
        $originHeader = array_pop($originHeaders);
        foreach (explode("\r\n", $originHeader) as $i => $header) {
            if ($i == 0 || !$header) {
                continue;
            }

            $header = explode(':', $header, 2);
            $header[0] = strtoupper($header[0]);
            if ($header[0] == 'SET-COOKIE') {
                $this->headers[$header[0]][] = isset($header[1]) ? trim($header[1]) : '';
            } else {
                $this->headers[$header[0]] = isset($header[1]) ? trim($header[1]) : '';
            }
        }

        return $this->headers;
    }

    /**
     * 获取原始响应头
     *
     * @return string
     */
    public function getOriginHeader()
    {
        return $this->originHeaders;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->body;
    }
}