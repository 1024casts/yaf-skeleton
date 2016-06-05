<?php

use Core\AbstractApi;
use App\Models\User;
use App\Models\Test;
use Yar\YarClient;
use Support\Log;
use Core\Http;

class IndexController extends AbstractApi
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
        // 获取
        $user = User::find(47);

        //$httpClent = new Http();
        //$httpClent->get('https://api.github.com/users/qloog/repos');
        Log::info('file nums:', get_included_files());
        $this->successJson($user);

    }

    public function mysqlAction()
    {
        $client = new test();
        $user = $client->find(41);
        dd($user); // dd 放到
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