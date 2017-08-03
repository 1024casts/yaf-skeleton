<?php

namespace Tests\Controllers;

use Tests\TestCase;

/**
 * 首页控制器测试类
 */
class IndexTest extends TestCase
{
    /**
     * 测试index方法
     */
    public function testIndex()
    {
        $request = new \Yaf\Request\Simple("CLI", "Index", "Index", 'test');
        $response = $this->_application->getDispatcher()->returnResponse(true)->dispatch($request);
        $content = $response->getBody();

        $this->assertEquals('index', $content);
    }

    /**
     * 测试index方法
     */
    public function testJson()
    {
        $request = new \Yaf\Request\Simple("CLI", "Index", "Index", 'json');
        $response = $this->_application->getDispatcher()->returnResponse(true)->dispatch($request);
        $content = $response->getBody();

        //$this->assertEquals('json', $content);
        $this->assertJsonStringEqualsJsonString('{"uid":1,"username":"admin"}', $content);
    }

    public function testArray()
    {
        $request = new \Yaf\Request\Simple("CLI", "Index", "Index", 'json');
        $response = $this->_application->getDispatcher()->returnResponse(true)->dispatch($request);
        $content = $response->getBody();

        $content = json_decode($content, true);
        $this->assertArrayHasKey('uid', $content);
    }

    public function testEquality() {
        $this->assertEquals(
            [1, 2, 3, 4, 5, 6],
            ['1', 2, 33, 4, 5, 6]
        );
    }

    public function testFailure()
    {
        $this->assertTrue(true);
    }
}