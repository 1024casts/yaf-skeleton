<?php

class UserController extends Yaf\Controller_Abstract
{


    public function init()
    {
        Yaf\Dispatcher::getInstance()->disableView();
    }

    /**
     * 使用 Eloquent ORM
     */
    public function mysqlAction()
    {
        //$user = UserModel::find(2);
        //echo $user->toJson();

        //$users = UserModel::get();
        $users = UserModel::where('email','=', 'test@test.com')->get();

        echo $users->toJson();
    }

}