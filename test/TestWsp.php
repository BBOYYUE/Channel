<?php

use Workerman\Worker;
use Workerman\Connection\AsyncTcpConnection;

require_once '../vendor/autoload.php';

$worker = new Worker();

$worker->onWorkerStart = function ($worker) {
    // 设置访问对方主机的本地ip及端口(每个socket连接都会占用一个本地端口)

    $con = new AsyncTcpConnection('ws://127.0.0.1:8086');

    $con->onConnect = function ($con) {
        $arr = ['method'=>'send','equipment_number'=>1,'message'=>'hello php'];
        $con->send(json_encode($arr));
    };

    $con->onMessage = function ($con, $data) {
//        echo $data;
    };

    $con->connect();
};

Worker::runAll();