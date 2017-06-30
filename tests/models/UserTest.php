<?php

class UserTest extends \Core\Tests\PHPUnit\ModelTestCase
{
    public function testGetUserName()
    {
        $model = new \UserModel();
        $userId = 1;
        $result = $model->getUserName($userId);
        $this->assertEquals('iceup', $result);

        $userId = 100;
        $result = $model->getUserName($userId);
        $this->assertFalse($result);
    }
}