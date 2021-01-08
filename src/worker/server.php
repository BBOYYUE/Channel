<?php

namespace Bboyyue\Channel\worker;

use Channel\Server as ServerMange;
use Illuminate\Support\Facades\Redis as RedisMange;
use Illuminate\Support\Str;
use Workerman\Worker;

class server extends ServerMange
{
    /*
     * server listen
     */
    function listen()
    {
        $this->_worker->onWorkerStart =function($worker){

        };
        Worker::runAll();
    }
}