<?php

namespace App\Models;

use Db\Model;

class Test extends Model
{

    public function find($id)
    {
        return $this->_db->select('users', array('id' => $id));
    }
}