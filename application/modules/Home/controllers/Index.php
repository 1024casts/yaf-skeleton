<?php

class IndexController extends BaseController
{

    /**
     * 首页
     *
     * @return string
     */
    public function indexAction()
    {
        $data = [
            'message' => 'test message'
        ];

        return $this->display('index', $data);
    }
}