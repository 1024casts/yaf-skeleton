<?php

namespace App\Models;

use Db\model;

class Test extends model
{

    public function find($id)
    {
        $where = array('id' => 41);
        $ret = $this->_db->select($where);
        var_dump($ret);
    }
}