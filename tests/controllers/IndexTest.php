<?php

use Core\Tests\PHPUnit\ControllerTestCase;

/**
 * 首页控制器测试类
 */
class IndexTest extends ControllerTestCase
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
}