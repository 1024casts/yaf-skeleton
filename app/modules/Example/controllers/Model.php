<?php

use PHPCasts\Mvc\Controller\Web;

class ModelController extends Web
{

    /** @var  UserModel */
    protected $userModel;

    public function init()
    {
        parent::init();

        $this->userModel = new UserModel();
    }

    public function EloquentAction()
    {
        $users = $this->userModel->all()->toArray();
        var_dump($users);
        $user = $this->userModel->find(1);
        var_dump($user);
        exit;
    }
}