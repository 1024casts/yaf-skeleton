<?php

use PHPCasts\Log\Log;
use Yaf\Request\Http;

class IndexController extends PHPCasts\Mvc\Controller\Web
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
        \Yaf\Dispatcher::getInstance()->disableView();
        echo 'i am a_b';
    }

    public function indexAction()
    {
        \Yaf\Dispatcher::getInstance()->disableView();

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
        \Yaf\Dispatcher::getInstance()->disableView();

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
        //Dispatcher::getInstance()->disableView(0);
        //Dispatcher::getInstance()->disableView();
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
