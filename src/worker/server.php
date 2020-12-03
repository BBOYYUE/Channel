<?php

namespace Bboyyue\Channel\worker;

use Channel\Server as ServerMange;
use Illuminate\Support\Facades\Redis as RedisMange;
use Illuminate\Support\Str;
use Workerman\Worker;

class server extends ServerMange
{
    /*
     *
     */
    function listen()
    {
        $this->_worker->onWorkerStart =function($worker){
            $worker_id = $worker->id;
            if(RedisMange::get('alpha_channel_server')) $worker_map = json_decode(RedisMange::get('alpha_channel_server'),true);
            $worker_map = isset($worker_map)&&is_array($worker_map)?array_push($worker_map,$worker_id):[$worker_id];
            RedisMange::set('alpha_channel_server',json_encode($worker_map));
        };
        Worker::runAll();
    }
}