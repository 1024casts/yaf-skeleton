<?php

namespace Db;

class Model
{
    public function __construct() {
        $this->_config = \Yaf\Registry::get("config");
        $this->_db = new Mysql ($this->_config->database->config->toArray());

        //$this->_redis = new Redis();
        //$this->_redis->connect($this->_config->redis->host);
    }
}