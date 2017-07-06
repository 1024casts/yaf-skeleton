<?php

use Core\Log;
use Yaf\Request\Http;

class IndexController extends Core\Mvc\Controller\Web
{
    /**
     * 初始化由yaf自动调用
     * @access public
     */
    public function init()
    {
        parent::init();
    }

    public function indexAction()
    {
        //// 获取
        //$user = UserModel::find(1);
        //echo $user->toJson();
        return 'index phtml';
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

    public function testAction()
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

    public function getdataAction()
    {
        return json_encode(['code' => 200, 'data' => ['user_id' => 1, 'user_name' => 'test1']]);
    }
}