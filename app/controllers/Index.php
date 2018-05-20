<?php

use PHPCasts\Yaf\Log\Log;

class IndexController extends PHPCasts\Yaf\Mvc\Controller\Web
{
    /**
     * 初始化由yaf自动调用
     * @access public
     */
    public function init()
    {
        parent::init();
    }

    public function aaaAction()
    {
        echo 'i am a_b';
    }

    public function indexAction()
    {
        echo 'Index';
    }

    public function testAction()
    {
        //// 获取
        //$user = UserModel::find(1);
        //echo $user->toJson();
        Log::info('test', $this->getServer());
        $this->getResponse()->setBody('index');
    }

    public function jsonAction()
    {
        $json = json_encode(['uid'=>1,'username'=>'admin']);
        $this->getResponse()->setBody($json);
    }

    /**
     * 此处会报php error, setBody参数必须是string
     */
    public function arrayAction()
    {
        //$this->getResponse()->setBody(['uid'=>1]);
    }

    /**
     * 使用 Eloquent ORM
     */
    public function mysqlAction()
    {
        // 获取
        $user = UserModel::find(1);
        echo $user->toJson();
    }

    public function pdoAction()
    {
        $client = new test();
        $user = $client->find(41);
        dd($user); // dd 放到
    }

    public function test1Action()
    {
        $client = new YarClient(
            array(
                'controller' => 'index',
                'action' => 'getdata',
            ),
            array('args' => 'some parameters', 'format' => 'json', 'http://yaf-api.local/')
        );
        $data = $client->api();
        print_r($data);
    }
}
