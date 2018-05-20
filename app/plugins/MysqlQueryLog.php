<?php

use Illuminate\Database\Capsule\Manager as Capsule;

class MysqlQueryLogPlugin extends Yaf\Plugin_Abstract
{

    public function dispatchLoopShutdown(Yaf\Request_Abstract $request, Yaf\Response_Abstract $response)
    {
        $queryLogs = Capsule::getQueryLog();

        foreach ($queryLogs as $log){
            $filename = STORAGE_PATH . '/sql/' . date('Y-m-d') . '.log';
            file_put_contents($filename, 'sql: ' . json_encode($log) . "\n", FILE_APPEND);
        }

    }
}