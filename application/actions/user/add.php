<?php

class AddAction extends Yaf\Action_Abstract
{
    public function execute()
    {
        echo 'my is add.';
        var_dump($uid == $this->getRequest()->getParam('uid'));
    }
}