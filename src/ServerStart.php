<?php
namespace Bboyyue\Channel;

use Channel\Server as ServerMange;
use Workerman\Worker;



class ServerStart
{
    public static function runAll(){

        $server = new ServerMange('127.0.0.1','2206');
        Worker::runAll();
    }
}

