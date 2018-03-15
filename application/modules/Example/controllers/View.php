<?php

class ViewController extends Yaf\Controller_Abstract
{

    public function testAction()
    {
        $this->getView()->assign('title', 'test title');
        $this->getView()->assign('content', 'test content');


        $data = [
            'title' => 'test title.',
            'content' => 'test content.'
        ];
        $this->getView()->display('view/test.phtml', $data);
    }
}