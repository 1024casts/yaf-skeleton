<?php

namespace Db;

class TaModel
{
    public function __construct()
    {
        var_dump(12312);

        //$this->_config = \Yaf\Registry::get("config");
        //$this->_db = new Mysql ($this->_config->database->config->toArray());

        //$this->_redis = new Redis();
        //$this->_redis->connect($this->_config->redis->host);

        var_dump($this->_config);exit;
        //$pdoClient = new pdo($this->_config->database->config->toArray());
        //var_dump($pdoClient);
        //exit;
        //$this->_db = $pdoClient->connect();
    }
}