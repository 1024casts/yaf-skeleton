<?php

/**
 * 首页控制器测试类
 */
class IndexTest extends \Core\Tests\PHPUnit\ControllerTestCase
{
    /**
     * 测试index方法
     */
    public function testIndex()
    {
        $request = new \Yaf\Request\Simple("CLI", "Index", "Index", 'index');
        $response = $this->_application->getDispatcher()
            ->returnResponse(true)
            ->dispatch($request);
        $content = $response->getBody();
        $this->assertEquals('index phtml', $content);
    }
}